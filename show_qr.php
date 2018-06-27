<script src="js/qrcode.min.js"></script>
<style>
    #qrcode
    {
        position: relative;
        left:330px;
        bottom:50px;
    }

    #qrcode_back_btn
    {
        position: relative;
        width: 250px;
        height: 40px;
        left:330px;
        bottom:20px;
    }

    @media (max-width: 768px)
    {
        #qrcode
        {
            position: relative;
            left:20px;
            bottom:70px;
        }

        #qrcode_back_btn
        {

            left:20px;

        }
    }
</style>
<section id="aa-myaccount">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-myaccount-area">
                    <div class="row">
                        <div class="col-md-12 col-md-offset-1">
                            <div id="qrcode"></div>
                            <input id="qrcode_back_btn" type="button" class="btn btn-primary" value="返回" onclick="location.href='index.php?url=manager_center'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<span id="manager_no" style="display:none;">
    <?php echo $_SESSION["manager_no"]."/".$member_id; ?>
</span>

<script>
    $(function ()
    {
        $("#aa-slider").hide();
        if($(window).width() < 767)
        {
            $("html,body").scrollTop(700);
        }
        else
        {
            $("html,body").scrollTop(70);
        }
    });

    var qrcode = new QRCode(document.getElementById("qrcode"),
        {
            text: $("#manager_no").text(),
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
</script>