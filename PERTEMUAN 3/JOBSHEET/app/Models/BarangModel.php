<?php

// Define the namespace for the model.
namespace App\Models;

// Import necessary classes and traits from Laravel's Eloquent package.
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Define the BarangModel class which extends the base Eloquent Model.
class BarangModel extends Model
{
    // Use the HasFactory trait to enable factory methods for testing and seeding.
    use HasFactory;
    
    // Define the table name that this model corresponds to.
    protected $table = 'm_barang';
    
    // Define the primary key for the table.
    protected $primaryKey = 'barang_id';

    // Define which attributes are mass assignable.
    // This array includes: 
    // - barang_id: The primary key for the product (often auto-incremented)
    // - kategori_id: The foreign key referencing the related category in m_kategori table
    // - barang_kode: The product code
    // - barang_nama: The product name
    // - harga_beli: The purchase price
    // - harga_jual: The selling price
    protected $fillable = ['barang_id', 'kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual','image'];

    // Define a relationship indicating that each Barang belongs to one Kategori.
    // This sets up a "belongs to" relationship with the KategoriModel.
    // The first parameter is the related model class.
    // The second parameter is the foreign key on this model (m_barang.kategori_id).
    // The third parameter is the local key on the related model (m_kategori.kategori_id).
    public function kategori(): BelongsTo {
        // The commented-out code indicates that at some point there might have been a different relationship (hasOne with LevelModel).
        // return $this->hasOne(LevelModel::class);
        
        // Return the relationship using belongsTo, which allows us to access the related category information.
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }
}
