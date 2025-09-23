<?php
session_start();
session_destroy();
?>
<script>
    localStorage.removeItem("user_id");
    window.location.href = "login.php";
</script>