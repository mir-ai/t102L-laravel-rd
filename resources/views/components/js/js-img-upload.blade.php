<script type="module">

$(function(){

    $('#message_image').on('change', function (e) {
        var fileset = $(this).val();
        if (fileset === '') {
            $("#img_preview").attr('src', "");
        } else {
            console.log(fileset);
            var reader = new FileReader();
            console.log(reader);
            reader.onload = function (e) {
                console.log(e.target);
                $("#img_preview").attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
            $('#img_delete').show();
        }
    });
    
    $('#img_delete').on('click', function (e) {
        $("#message_image").val('');
        $("#img_preview").attr('src', "");
        $("#img_url").val('');
        $("#img_url_s").val('');
        $(this).hide();

        return false;
    });

});

</script>

