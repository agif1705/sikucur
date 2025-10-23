<div>
    <table class="w-full text-left border-collapse text-xs">
        <tbody>
            @foreach ($data as $key => $value)
                <tr class="border-b">
                    <td class="py-1 px-2">{{ $key }}</td>
                    <td class="py-1 px-2">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
