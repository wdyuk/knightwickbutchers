<head>
	<meta charset="utf-8">
	<title><?= SITE_NAME;?> | <?php echo $pageData['title']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="<?php echo $pageData['meta_keywords']; ?>" />
	<meta name="description" content="<?php echo $pageData['meta_description']; ?>">
  <meta name="google-site-verification" content="<?= GOOGLE_SITE_VERIFICATION; ?>" />
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Pinyon+Script" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles-merged.css">
    <link rel="stylesheet" href="/css/style.css?v1.1">
    <link rel="stylesheet" href="/assets/theme/css/custom.css?v1.1">
    <link rel="stylesheet" href="/css/helper.css?v1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/theme/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/theme/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/theme/img/favicon-16x16.png">
    <link rel="manifest" href="/assets/theme/img/site.webmanifest">
    <link rel="mask-icon" href="/assets/theme/img/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/assets/theme/img/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/assets/theme/img/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <!--[if lt IE 9]>
      <script src="js/vendor/html5shiv.min.js"></script>
      <script src="js/vendor/respond.min.js"></script>
    <![endif]-->
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="/js/scripts.min.js"></script>
    <script src="/js/custom.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-179593153-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?= GOOGLE_ANALYTICS; ?>');
    </script>
</head>