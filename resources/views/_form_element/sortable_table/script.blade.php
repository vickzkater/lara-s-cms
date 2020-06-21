<!-- jQuery UI -->
<script src="{{ asset('admin/vendors/jquery-ui/jquery-ui-git.js') }}"></script>
<script>
    $(document).ready(function() {
        if (typeof AjaxSortingURL == 'undefined') {
            alert("You must set variable (AjaxSortingURL)");
        }

        $('.sorted_table').sortable({
            axis: 'y',
            update: function (event, ui) {
                var data_list = $(this).sortable('serialize');
                $.ajax({
                    type: 'POST',
                    url: AjaxSortingURL,
                    data: {
                        _token: '{{ csrf_token() }}',
                        rows: data_list,
                    },
                    success: function(data){
                        console.log(data);
                    },
                    error: function (data, textStatus, errorThrown) {
                        console.log(data);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        });
    });
</script>