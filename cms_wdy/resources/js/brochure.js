$(function () {
    function checkOverflow() {
        $(".editable").each( function() { 
            var $el = $(this);
            var visibleheight = $el.height() - $el.position().top;
            if ($el.height() > visibleheight) {
                // $(this).addClass('error-overflow');
            } else {
                $(this).removeClass('error-overflow');
            }
        
        })
    }
    
    $('#print').on('click', function() {
       var size = $('input[name=layout-select]:checked').data('size');
       $('#layout-area').printThis({
        loadCSS: "/resources/css/brochure-print-"+size+".css"
       });
    
    });
    
    $('input[name="layout-select"]').on('click', function() {

        $('.back-to-layouts').show();

        if($(this).data('size') == 'a3') {
           
                 $('#layout-area').removeClass('A4');
           
                $('#layout-area').addClass('A3');

        } else {

                 $('#layout-area').removeClass('A3');
            
            
                $('#layout-area').addClass('A4');
            
        }
    });

    $(document).on('click','.brochure-modal-save', function() {
        var dataId = $(this).data('id');
        var field = $(this).data('field');
        var property_id = $(this).data('property_id');
        console.log(dataId); 
        var save_value = CKEDITOR.instances["ck"+dataId+""].getData();
        console.log(save_value);
        $.ajax({
            type: "POST",
            url: "/ajax/update-brochure.php", //get response from this file
            dataType: "json",
            data: {
                property_id: property_id,
                field: field,
                val: save_value
            },
            success: function (response) {

                $('.'+field+"-modal-open").html(save_value).animate({
                  backgroundColor: "rgba(50,100,240,0.4)"
                }, 500).animate({
                    backgroundColor: "rgba(50,240,100,0.4)"
                }, 500);
            }
        });
    });

    $(document).on('focusin', '#layout-area .editable input, #layout-area .editable textarea', function(){
        $(this).data('val', $(this).val());
    }).on('input','#layout-area .editable input, #layout-area .editable textarea', function(){ 

        var prev = $(this).data('val');
        var current = $(this).val();

        if (prev != current) {
            $(this).css("background","rgba(50,100,240,0.6)");
        } else {
            $(this).css("background","rgba(255,255,255,1)");
        }
    }).on('change','#layout-area .editable input, #layout-area .editable textarea', function(){ 
        var save_value = $(this).val();
        var field = $(this).data('field');
        var property_id = $(this).data('property_id');
        $(this).css("background","rgba(50,100,240,0.6)");
        // saved
        $.ajax({
            type: "POST",
            url: "/ajax/update-brochure.php", //get response from this file
            dataType: "json",
            data: {
                property_id: property_id,
                field: field,
                val: save_value
            },
            success: function (response) {

                alert('Saved!');
            }
        });
        $(this).css("background","rgba(50,240,100,0.4)");
    })


    var LayoutHandler = {
    fn: {
        start: function () {
            $('div.img-container').each(function () {
                var div = $(this);
                $('<span class="remove"/>').text('X').appendTo(div);
            });


            $('img.thumb').draggable({
                containment: '#layout-area',
                revert: 'invalid',
                helper: 'clone'
            });

            $('div.img-container').droppable({
                accept: 'img.thumb',
                drop: function (event, ui) {
                    var div = $(this);
                    var img = ui.draggable;
                    var copy = img.clone();
                    $(copy).addClass('sized').appendTo(div);
                    div.addClass('img-inserted');
                    $('span.remove', div).show();
                    $.ajax({
                        type: "POST",
                        url: "/ajax/update-brochure-images.php", //get response from this file
                        dataType: "json",
                        data: {
                            property_id: div.data('property-id'),
                            template_id: div.data('template'),
                            placeholder_id: div.data('placeholder-id'),
                            image_id: img.data('image-id')
                        },
                        success: function (response) {

                            div.animate({
                              opacity: 0.4
                            }, 500).animate({
                                 opacity: 1
                            }, 500);
                        }
                    });
                }

            });


        },

        remove: function () {
            $(document).on('click', 'span.remove', function () {
                var span = $(this);
                var div = span.parent();
                var img = div.find('img');
                $.ajax({
                    type: "POST",
                    url: "/ajax/update-brochure-images.php", //get response from this file
                    dataType: "json",
                    data: {
                        property_id: div.data('property-id'),
                        template_id: div.data('template'),
                        placeholder_id: div.data('placeholder-id'),
                        image_id: img.data('image-id'),
                        delete: true
                    },
                    success: function (response) {
                        div.animate({
                          opacity: 0.4
                        }, 500).animate({
                             opacity: 1
                        }, 500);

                        img.remove();
                        div.removeClass('img-inserted');
                        span.remove();
                    }
                });
            });
        },

        oninput: function () {
            $('input[type="text"]', '#layout-area').focus();
        },

        chooseLayout: function () {
            $('input[type="radio"]', '#choose-layout').change(function () {
                var $input = $(this);
                if ($input.prop('checked')) {
                    var target = $('#' + $input.val());
                    target.show().siblings('table').hide();
                    $input.parents('#choose-layout').find('input').not($input).removeAttr('checked');
                    $('#choose-layout').hide();
                    checkOverflow();
                }
            });
        },
    },

    init: function () {
        for (var method in this.fn) {
            this.fn[method]();
        }

    }
};


LayoutHandler.init();

  var a = function() {
    var b = $(window).scrollTop();
    var d = $("#scroller-anchor").offset().top;
    var c=$("#thumbs");
    if (b>(d+150)) {
        c.css({position:"sticky",top:"0px",'z-index':"999999"});
        $('#thumbs img').css({width: "150px"});
        $('#thumbs .thumb-image-container').css({width: "60px", height: "60px", "border-radius": "10px"});
    } else {
        c.css({position:"relative",top:""})
        $('#thumbs img').css({width: "200px"});
        $('#thumbs .thumb-image-container').css({width: "120px", height: "120px", "border-radius": "20px"});
    }
  };
    $(window).scroll(a);a()

    // $("textarea").each( function( i, el ) {
    //     $(el).height( el.scrollHeight );
    // ​});
    // function autoresize(textarea) {
    //     textarea.style.height = '0px';     //Reset height, so that it not only grows but also shrinks
    //     textarea.style.height = (textarea.scrollHeight+10) + 'px';    //Set new height
    // }
    // $('textarea').keyup(function () {
    //     autoresize(this);
    // });
    
    $('.features-modal-open').click( function() {
        $('#edit-brochure-features').modal('toggle');
    })
    $('.description-modal-open').click( function() {
        $('#edit-brochure-description').modal('toggle');
    })
    $('.location-modal-open').click( function() {
        $('#edit-brochure-location').modal('toggle');
    })
    
});
