$('#openModal').click(function() {
        let url = $(this).data('action');
        console.log(url);
        $('#bookModal').show();
        $('#formData').trigger("reset");
        $('#formData').attr('action',url);
        $('#name').focus();
})
    // Event for created and updated posts
    $('#formData').submit(function(e) {
        e.preventDefault();
         let formData = new FormData(this);
         $.ajax({
            type: 'POST',
            data : formData,
            contentType: false,
             processData: false,
             url: $(this).attr('action'),
             beforeSend:function(){
             $('#btn-create').addClass("disabled").html("Processing...").attr('disabled',true);
         $(document).find('span.error-text').text('');
         },
         complete: function(){
         $('#btn-create').removeClass("disabled").html("Save   Change").attr('disabled',false);
         },
         success: function(res){
         console.log(res);
         if(res.success == true){
         $('#formData').trigger("reset");
         $('#bookModal').hide();
         Swal.fire(
         'Success!',
         res.message,
         'success'
         ).then((result) => {
          location.reload();
         })
         }
         },
         error(err){
         $.each(err.responseJSON,function(prefix,val) {
         $('.'+prefix+'_error').text(val[0]);
         })
         console.log(err);
         }
         })
    })



//delete
  $(document).on('click','#btn-delete',function(e) {
    e.preventDefault();
    let dataDelete = $(this).data('id');
    // console.log(dataDelete);
    Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this! ",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
    if (result.isConfirmed) {
    $.ajax({
    type:'DELETE',
    dataType: 'JSON',
    url: `/admins/books/${dataDelete}`,
    data:{
    '_token':$('meta[name="csrf-token"]').attr('content'),
    },
    success:function(response){
    Swal.fire(
    'Deleted!',
    'Your file has been deleted.',
    'success'
    )
    // showUsers()
    location.reload();

    },
    error:function(err){
    console.log(err);
    }
    });
    }
    })
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          var div_id  = $(input).attr('set-to');
          console.log(div_id);
          reader.onload = function (e) {
              $('#'+div_id).attr('src', e.target.result);
 
          }
          reader.readAsDataURL(input.files[0]);
      }
  }
  $(".img").change(function(){
      readURL(this);
  });