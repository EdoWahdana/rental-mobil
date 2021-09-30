<?php 
    session_start();
    include('includes/config.php');
?>

<style>
.adiv {
    background: #f59161;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    font-size: 12px;
    height: 46px
}
.chat-btn {
    position: fixed;
    right: 40px;
    bottom: 40px;
    cursor: pointer
}

.chat-btn .close {
    display: none
}

.chat-btn i {
    transition: all 0.9s ease
}

#check:checked~.chat-btn i {
    display: block;
    pointer-events: auto;
    transform: rotate(180deg)
}

#check:checked~.chat-btn .comment {
    display: none
}

.chat-btn i {
    font-size: 22px;
    color: #fff !important
}

.chat-btn {
    width: 60px;
    height: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    background-color: #fa2837;
    color: #fff;
    font-size: 15px;
    border: none;
    box-shadow: 2px 2px 10px #1716165e;
    z-index: 1;
}

.wrapper {
    position: fixed;
    right: 20px;
    bottom: 100px;
    width: 300px;
    height: 400px;
    overflow: auto;
    background-color: #ffffff;
    border-radius: 5px;
    opacity: 0;
    transition: all 0.4s;
    z-index: 1;
    box-shadow: 5px 5px 20px black;
}

#check:checked~.wrapper {
    opacity: 1
}

.chat-form {
    display: block;
    position: relative;
    bottom: 0px;
    padding: 20px;
}

.chat-form input,
textarea,
button {
    margin-bottom: 10px;
    font-size: 15px;
}

.chat-form textarea {
    resize: none
}

.form-control:focus,
.btn:focus {
    box-shadow: none
}

#check {
    display: none !important
}

.card {
    width: 300px;
    border: none;
    border-radius: 15px
}

.chat {
    border: none;
    background: #E2FFE8;
    font-size: 13px;
    border-radius: 10px;
    padding: 0px 15px 10px;
}

.bg-white {
    background: #FFF;
}

.ml-auto {
	margin-right: auto !important;
}
.mr-auto {
	margin-left: auto !important;
}
.d-flex {
    display: -ms-flexbox !important;
    display: flex !important;
}
.flex-row {
    -ms-flex-direction: row !important;
    flex-direction: row !important;
    margin-bottom: 7px;
}
.text-white {
    color: #FFF;
    padding-top: 5px;
}
</style>

<?php if (isset($_SESSION['login'])) { ?>
<input type="checkbox" id="check"> <label class="chat-btn" for="check"> <i class="fa fa-commenting-o comment"></i></label>
<div class="wrapper">
<div class="d-flex justify-content-center">
    <div class="card">
        <div class="p-3 adiv text-center"> <h6 class="text-white">Chat Admin</h6></div>
            <div class="chat-group" style="margin-top: 10px;"></div>
            <div class="chat-form"> 
                <input type="hidden" id="id_customer" value="<?= $_SESSION['id_user'] ?>">
                <input type="hidden" id="timestamp" value="<?= date('Y-m-d H:i:s') ?>">
                <div class="form-group px-3"> <textarea class="form-control" id="pesan" rows="2" placeholder="Tulis pesan anda..."></textarea> </div>
                <button type="submit" id="send-chat" class="btn btn-success btn-block">Kirim Pesan</button> 
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $(document).on('click', '#send-chat', function() {
        var id_customer = $('#id_customer').val();
        var timestamp = $('#timestamp').val();
        var pesan = $('#pesan').val();
        $.ajax({
            url: "includes/chat_helper.php",
            method: "POST",
            dataType: 'text',
            data: {
                id_customer: id_customer,
                timestamp: timestamp,
                pesan: pesan
            },
            success: function(data) {
                $('#check').prop('checked', false);
                $('#pesan').val(''); 
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
                alert("error : " + textStatus);
            }
        });
    });

    $(document).on('click', '#check', function() {  
        var id_customer = $('#id_customer').val();      
        $('.chat-group').empty();
        $.ajax({
            url: "includes/chat_helper.php",
            method: "POST",
            dataType: 'json',
            data: {id_customer: id_customer},
            success: function(data) {
                console.log(data);
                $.each(data, function(index, item) {
                    if(item.id_admin != 0) {
                        $('.chat-group').append(`<div class="d-flex flex-row p-3"> <img src="https://img.icons8.com/color/48/000000/circled-user-female-skin-type-7.png" width="30" height="30">
                        <div class="chat ml-auto p-3"><span class="text-muted dot" id="text-admin">${item.message}</span></div>
                        </div>`)
                    }   
                    else if(item.id_admin == 0) {
                        $('.chat-group').append(`<div class="d-flex flex-row p-3">
                        <div class="chat mr-auto p-3"><span class="text-muted" id="text-user">${item.message}</span></div>
                        </div>`)
                    }
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
                alert("error : " + textStatus);
            }
        });
    });
});
    
</script>

<?php } ?>