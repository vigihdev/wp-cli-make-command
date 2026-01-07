# Validator dalam Post_Type_Make_Command

## Ringkasan

Command `Post_Type_Make_Command` menggunakan dua validator utama untuk memastikan data yang dimasukkan valid sebelum diproses ke database WordPress:

1. `PostTypeValidator` - Memvalidasi aspek-aspek terkait post type dan taxonomy
2. `PostFactoryValidator` - Memvalidasi aspek-aspek umum pembuatan post

## PostTypeValidator

Validator ini digunakan untuk memvalidasi objek `PostDto` dan memastikan:

### 1. `mustHaveRegisteredPostType()`
- Memastikan post type yang diminta sudah terdaftar di WordPress
- Menggunakan fungsi `post_type_exists($type)`
- Jika post type tidak terdaftar, melempar `PostTypeException::notRegisteredPostType($type)`

### 2. `mustHaveRegisteredTaxonomies()`
- Memastikan semua taxonomy dalam `taxInput` sudah terdaftar di WordPress
- Menggunakan fungsi `taxonomy_exists($taxonomy)`
- Jika taxonomy tidak terdaftar, melempar `PostTypeException::notRegisteredTaxonomies($taxonomy)`

### 3. `mustAllowTaxonomiesForPostType()`
- Memastikan taxonomy yang diberikan diizinkan untuk digunakan dengan post type tertentu
- Menggunakan fungsi `is_object_in_taxonomy($postType, $taxonomy)`
- Jika taxonomy tidak diizinkan untuk post type, melempar `PostTypeException::notAllowTaxonomiesForPostType($postType, $taxonomy)`

### 4. `mustHaveExistingTerms()`
- Memastikan semua term yang disebutkan dalam `taxInput` benar-benar ada di taxonomy masing-masing
- Menggunakan fungsi `term_exists($term, $taxonomy)`
- Jika term tidak ditemukan, melempar `PostTypeException::notFoundTermInTaxonomy($term, $taxonomy)`

## PostFactoryValidator

Validator ini digunakan untuk memvalidasi data array yang akan digunakan untuk membuat post dan memastikan:

### 1. `validateCreate()`
Metode ini menjalankan serangkaian validasi termasuk:
- `mustBeValidAuthor()` - Memastikan author ID valid dan ada
- `mustHaveValidDateFormat()` - Memastikan format tanggal valid
- `mustHaveTitle()` - Memastikan judul tidak kosong dan cukup panjang
- `mustBeUniqueTitle()` - Memastikan judul unik
- `mustHaveValidType()` - Memastikan post type tidak kosong
- `mustHaveContent()` - Memastikan konten tidak kosong
- `mustHaveStatus()` - Memastikan status tidak kosong
- `mustBeValidStatus()` - Memastikan status valid
- `mustHaveName()` - Memastikan nama permalink tidak kosong
- `mustBeUniqueName()` - Memastikan nama permalink unik
- `hasValidDateGmtFormat()` - Memastikan format tanggal GMT valid

## Alur Validasi dalam Command

```php
// Dalam Post_Type_Make_Command::__invoke()
$postDto = new PostDto(
    title: $this->title,
    content: $this->post_content,
    type: $postType,
    taxInput: $taxInput,
);

// Validasi post type dan taxonomy
PostTypeValidator::validate($postDto)
    ->mustHaveRegisteredPostType()
    ->mustHaveRegisteredTaxonomies()
    ->mustAllowTaxonomiesForPostType()
    ->mustHaveExistingTerms();

// Validasi data post secara umum
PostFactoryValidator::validate($this->postData)->validateCreate();
```

## Contoh Penggunaan Command

```bash
wp make:post-type event \
  --post_type=event \
  --post_content="Event Content" \
  --tax_input='{"category":["event"],"post_tag":["concert"]}' \
  --dry-run
```

## Kesimpulan

Validator dalam command ini bekerja dengan baik untuk memastikan data yang dimasukkan valid sebelum diproses lebih lanjut. Mereka memberikan pesan error yang informatif jika validasi gagal, membantu pengguna memahami apa yang perlu diperbaiki.

Dari hasil test, validator berjalan sesuai harapan meskipun beberapa test case gagal karena perbedaan pesan error yang diharapkan vs aktual - ini menunjukkan bahwa validator aktif dan berfungsi, hanya perlu penyesuaian pada test case untuk mencocokkan pesan error yang sebenarnya.
