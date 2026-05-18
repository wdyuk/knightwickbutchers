<script>

$('.newckeditor').each(function(){
  CKEDITOR.replace( $(this).attr('id') ,{
    filebrowserUploadUrl : '<?= ADMIN_URL; ?>/upload.php'
  });
});

</script>
