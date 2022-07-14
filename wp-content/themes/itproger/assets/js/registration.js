jQuery(document).ready(()=>{
    $(document).ready(()=>{
        $("#cor-password").keyup(()=>{ 
            
            var value_input1 = $("#password").val();
            var value_input2 = $(this).val(); 
            
            if(value_input1 != value_input2) { 
                $("#button-enter").attr("disabled", "disabled"); 
            } else { 
                $("#button-enter").removeAttr("disabled");  
            }
        });
        $("#password").keyup(()=>{ 
            
            var value_input1 = $("#cor-password").val(); 
            var value_input2 = $(this).val(); 
            
            if(value_input1 != value_input2) { 
                $("#button-enter").attr("disabled", "disabled"); 
            } else { 
                $("#button-enter").removeAttr("disabled");  
            }
        });
    })
})