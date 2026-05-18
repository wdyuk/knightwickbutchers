<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title; ?></title>
  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="./resources/css/tempusdominus-bootstrap-4.min.css" />
    <link rel="stylesheet" href="./resources/css/main.css">
    <link rel="stylesheet" media="screen" href="./resources/dropzone/basic.min.css">
    <link rel="stylesheet" media="screen" href="./resources/dropzone/dropzone.min.css">
    <link rel="stylesheet" media="screen" href="./resources/lightbox/css/lightbox.min.css">
    <link rel="stylesheet" media="screen" href="./resources/js/lib/jquery-ui/jquery-ui.min.css">
    <link rel="shortcut icon" type="image/png" href="./resources/images/wdy-logo-square.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <script src='./resources/js/lib/moment.min.js'></script>
    <script src='./resources/js/lib/jquery.min.js'></script>
    <script src='./resources/js/lib/jquery-ui/jquery-ui.min.js'></script>
    <script src='./resources/dropzone/dropzone.min.js'></script>
    <script src='./resources/js/jquery.doubleScroll.js'></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

    
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <script type="text/javascript" src="./resources/js/tempusdominus-bootstrap-4.min.js"></script>        
    <script src="./resources/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
    <script src='./resources/lightbox/js/lightbox.min.js'></script>
   
    <?php if ((isset($_GET['module']) && $_GET['module'] == 'brochure') && (isset($_GET['action']) && $_GET['action'] == 'brochure')) { ?>
        <script src='./resources/js/brochure.js'></script>
        <script src='./resources/js/printthis.js'></script>
        <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>

    <?php } ?>  
    
</head>
