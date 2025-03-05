<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>level</title>
</head>

<body>
    <h1>Data User</h1>
    <table border="1" cellpading="2" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Usernam</th>
            <th>Nama</th>
            <th>ID Level Pengguna</th>
        </tr>

        <tr>
            <td>{{ $data->user_id }}</td>
            <td>{{ $data->username }}</td>
            <td>{{ $data->nama }}</td>
            <td>{{ $data->level_id}}</td>
        </tr>

        <!-- <tr>
            <th>Jumlah Pengguna</th>
        </tr>
        <tr>
            <td>{{ $data }}</td>
        </tr> -->

    </table>
</body>

</html>