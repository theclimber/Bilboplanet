(function () {
    if(/iPhone|iPod|android|opera mobile|blackberry|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine|windows ce; ppc;|windows ce; smartphone;|windows ce; iemobile/i.test(navigator.userAgent)){
        var question = confirm("Do you want to see the mobile version of this site ?")
        if (question){
            window.location = "./mobile.php";
        }
    }
}());
