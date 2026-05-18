<?php $faqs = table_fetch_rows('faqs','status=1', 'id ASC');

foreach($faqs as $faq) {
    ?>
    <div class="faq px-3 py-3 blue-bg mb-2 text-left">
        <div class="question py-3"><p class="resizable mb-0 fw-700"><?= $faq['question']; ?><i class="pl-3 lilac fa icon-chevron-down float-right"></i></h5></div>
        <div class="answer pt-2" style="display:none;" data-id="<?= $faq['id']; ?>"><p class="resizable"><?= $faq['answer']; ?></p></div>
    </div>
    <?php
}
?>

<script>
    $(function() {
        $('.question').on('click', function(e) {
            e.preventDefault();
            if ($(this).hasClass('closed')) {
                $(this).removeClass('closed').addClass('open');
                $(this).next().hide(200);
            }else{
                $(this).removeClass('open').addClass('closed');
                $(this).next().show(200).siblings('div.answer').hide();
            }
        
            $(this).find('i').toggleClass('icon-chevron-down').toggleClass('icon-chevron-up');
            $(this).next().find('i').toggleClass('icon-chevron-down').toggleClass('icon-chevron-up');
        })
    })
</script>