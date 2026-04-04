<div class="p-6 bg-white rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-6">🏆 Overall Leaderboard</h2>

    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-4">Rank</th>
                <th class="px-6 py-4">Student Name</th>
                <th class="px-6 py-4 text-right">Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $index => $item)
                <tr class="border-t">
                    <td class="px-6 py-4 font-semibold">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">{{ $item['name'] }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">{{ $item['score'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
