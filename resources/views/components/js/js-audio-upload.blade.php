<script type="module">

$(function(){

    $('#message_audio').on('change', function (e) {
        var fileset = $(this).val();
        if (fileset === '') {
            $("#audio_preview").attr('src', "");
            $("#audio_preview").hide();
        } else {
            console.log(fileset);
            var reader = new FileReader();
            console.log(reader);
            reader.onload = function (e) {
                console.log(e.target);
                $("#audio_preview").attr('src', e.target.result);
                $("#audio_preview").show();
            }
            reader.readAsDataURL(e.target.files[0]);
            $('#audio_delete').show();
        }
    });
    
    $('#audio_delete').on('click', function (e) {
        $("#message_audio").val('');
        $("#audio_preview").attr('src', "");
        $("#audio_preview").hide();
        $("#message_wav").val('');
        $(this).hide();

        return false;
    });

});

</script>

