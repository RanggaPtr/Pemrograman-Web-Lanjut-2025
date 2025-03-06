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
            <th>Username</th>
            <th>Nama</th>
            <th>ID Level Pengguna</th>
            <th>Aksi</th>
        </tr>
        @foreach ( $data as $d )
        
        <tr>
            <td>{{$d->user_id  }}</td>
            <td>{{$d->username  }}</td>
            <td>{{$d->nama  }}</td>
            <td>{{$d->level_id  }}</td>
            <td><a href="/Pemrograman%20Web%20Lanjut/PERTEMUAN%202/JOBSHEET/public/user/ubah/{{ $d->user_id }}">Ubah</a> | <a href="/Pemrograman%20Web%20Lanjut/PERTEMUAN%202/JOBSHEET/public/user/hapus/{{ $d->user_id }}">Hapus</a> </td>
        </tr>
        @endforeach
        

    </table>
</body>

</html>