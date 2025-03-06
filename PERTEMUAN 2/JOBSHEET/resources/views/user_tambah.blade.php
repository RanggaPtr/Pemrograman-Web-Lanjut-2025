<body>
    <h1>Form tambah data user</h1>
    <form method="post" action="/Pemrograman%20Web%20Lanjut/PERTEMUAN%202/JOBSHEET/public/user/tambah_simpan">

        {{ csrf_field() }}

        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan Username">
        
        <label>Nama</label>
        <input type="text" name="nama" placeholder="Masukkan Nama">
        
        <label>Password</label>
        <input type="text" name="password" placeholder="Masukkan Password">
        
        <label>Level ID</label>
        <input type="text" name="level_id" placeholder="Masukkan ID Level">
         
        <br><br>
        <input type="submit" class="btn btn-success" value="Simpan">
        
    </form>
</body>