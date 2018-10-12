$( function() {
    $( "#sortable" ).sortable({
        update: function () {
            var inputs = $('#itemList').serialize();
            var url = window.location.href;
            var urlArr = url.split('/');
            var productId = urlArr[4];
            $.ajax({
                type: 'post',
                url: '/product/'+ productId +'/edit',
                data: inputs,
                success: function(data) {

                }
            });
        }
    });
    $( "#sortable" ).disableSelection();
} );



