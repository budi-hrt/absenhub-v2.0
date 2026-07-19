<x-table :headers="[['key' => 'name', 'label' => 'Name']]" :rows="[['name' => 'Jane']]">
    @scope('cell_name', $row)
        <span class="text-success">Hello {{ $row['name'] }}</span>
    @endscope
</x-table>