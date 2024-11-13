<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class TailFormsInputText extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $value,
        public string $label,
        public string $name,
    )
    {
        // dump($this);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // $x = new ComponentAttributeBag();
        // $x->setAttributes(['x'=>'y']);
        // $this->attributes = $x;


        return view('components.tail-forms-input-text');
    }
}
