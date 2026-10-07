<?php
namespace Tests\Feature;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;
class BackupScheduleTest extends TestCase {
    public function test_only_explicitly_enabled_backups_are_scheduled_even_in_sandbox(): void {
        $this->assertCount(0, app(Schedule::class)->events());
        config(['backup.enabled'=>true]); app()->forgetInstance(Schedule::class);
        $events = app(Schedule::class)->events();
        $this->assertCount(3, $events);
        $this->assertSame('15 3 * * *', $events[0]->expression);
        $this->assertSame('Europe/Skopje', $events[0]->timezone);
        $this->assertTrue($events[0]->withoutOverlapping);
        $this->assertSame(['backups'], config('backup.backup.destination.disks'));
    }
}
