<script src="js/qrcode.min.js"></script>
<style>
	#qrcode
	{
		margin-top:180px;
		margin-left:480px;
	}
	
	#qrcode_back_btn
	{
		width:180px;
		margin-left:480px;
		margin-bottom:95px;
	}

    @media (max-width:768px)
	{
		#qrcode
		{
			margin-top:180px;
			margin-left:95px;
		}
	
		#qrcode_back_btn
		{
			width:180px;
			margin-left:95px;
		}
	}
</style>

<div id="qrcode"></div><br>
<input id="qrcode_back_btn" type="button" class="btn btn-primary" value="返回" onclick="location.href='index.php?url=bonus_use'">


<span id="manager_no" style="display:none;">
    <?php echo $_GET['bid']."/".$_GET['id']; ?>
</span>

<script>
    $(function ()
    {
        $("#aa-slider").hide();
        if($(window).width() < 768)
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
            width: 180,
            height: 180,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
</script>