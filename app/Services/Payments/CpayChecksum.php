<?php
namespace App\Services\Payments;

class CpayChecksum
{
    public function sign(array $fields, string $secret): array {
        $lengths = '';
        foreach ($fields as $value) {
            $length = mb_strlen((string) $value, 'UTF-8');
            if ($length > 999 || !is_scalar($value)) throw new \InvalidArgumentException('Invalid payment field.');
            $lengths .= str_pad((string) $length, 3, '0', STR_PAD_LEFT);
        }
        $header = str_pad((string) count($fields), 2, '0', STR_PAD_LEFT).implode(',', array_keys($fields)).','.$lengths;
        return ['header' => $header, 'checksum' => strtoupper(md5($header.implode('', $fields).$secret))];
    }

    /** Parse the bank's parameter order; never trust unsigned extra fields. */
    public function verify(array $input, string $secret): array {
        $header = $input['ReturnCheckSumHeader'] ?? null;
        $checksum = $input['ReturnCheckSum'] ?? null;
        $invalid = fn () => abort(422, 'Invalid bank response.');
        if (!$secret || !is_string($header) || strlen($header) > 4096 || !is_string($checksum) || !preg_match('/^[a-f0-9]{32}$/iD', $checksum)) $invalid();
        if (!preg_match('/^([0-9]{2})(.*)$/sD', $header, $parts)) $invalid();
        $count = (int) $parts[1];
        $chunks = explode(',', $parts[2]);
        $lengths = array_pop($chunks);
        if ($count < 8 || $count > 50 || count($chunks) !== $count || count(array_unique($chunks)) !== $count || !preg_match('/^[0-9]{'.($count * 3).'}$/D', $lengths)) $invalid();
        $fields = [];
        foreach ($chunks as $i => $name) {
            if (!preg_match('/^[A-Za-z][A-Za-z0-9]{0,49}$/D', $name) || !isset($input[$name]) || !is_scalar($input[$name])) $invalid();
            $fields[$name] = (string) $input[$name];
            if (mb_strlen($fields[$name], 'UTF-8') !== (int) substr($lengths, $i * 3, 3)) $invalid();
        }
        if (!hash_equals(strtoupper(md5($header.implode('', $fields).$secret)), strtoupper($checksum))) $invalid();
        return $fields;
    }
}
