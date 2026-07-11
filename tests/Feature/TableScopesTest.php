<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class TableScopesTest extends TestCase
{
    public function test_scoped_table_slots_render_html_without_escaping(): void
    {
        $html = Blade::render(<<<'BLADE'
        <x-table :headers="[['key' => 'name', 'label' => 'Name']]" :rows="[['name' => 'Jane']]">
            @scope('cell_name', $row)
                <span class="text-success">Hello {{ $row['name'] }}</span>
            @endscope
        </x-table>
        BLADE);

        $this->assertStringContainsString('text-success', $html);
        $this->assertStringContainsString('Hello Jane', $html);
        $this->assertStringNotContainsString('&lt;span', $html);
    }
}
