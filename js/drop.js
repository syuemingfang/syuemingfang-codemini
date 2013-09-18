(function(a){
    // Browser: Firefox Chrome IE10 //
    dragover=function(evt){
        evt.preventDefault();
    }
    drop=function(evt){
        evt.preventDefault();
        // Declared //
        var f=evt.dataTransfer.files, //Get Files By DataTransfer
        fd=new FormData(),
        xhr=new XMLHttpRequest(),
        progress=document.getElementById('progress');
        // Connect PHP //
        xhr.open('POST', 'index.php');
        xhr.upload.onprogress=function(evt){
            if(evt.lengthComputable){
                var complete=(evt.loaded/evt.total*100|0);
                if(100 == complete){
                    complete=99.9;
                }
                progress.innerHTML=complete+'%';
            }
        }
        xhr.onload=function(){
            progress.innerHTML='100%';
            location.href='mini.zip';
        }
        // Connect Preview //
        for(var i in f){
            var fr=new FileReader();
            fr.readAsDataURL(f[i]);
            fd.append('ff[]', f[i]);
        }
        fd.append('zone', 'upload');
        xhr.send(fd);
    }
    a.drag=this;
    return this;
})(window);