
<div id="generate-custom-cards" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" id="generate-custom-cards-modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Generate Virtual Cards')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center ">

                <form id="generate-cards-form" onsubmit="closeModal(this)" method="POST" action="{{route('organisation_cards.generate_custom_cards', $organisation->id)}}" target="_blank">
                    @csrf

                    <div class="form-group row" >
                        <label class="col-md-5 fs-15 col-form-label">{{translate('Prefix Code')}}</label>
                        <div class="col-md">
                            <input type="text" placeholder="{{translate('Prefix Code')}}"
                                   required  name="prefix_code" class="form-control remove-all-spaces toUpperCase">
                        </div>
                    </div>

                    <div class="form-group row" >
                        <label class="col-md-5 fs-15 col-form-label">{{translate('Number of Cards')}}</label>
                        <div class="col-md">
                            <input type="number" placeholder="{{translate('Number of Cards')}}"
                                   required step="1"  name="cards_num" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-primary mt-2" >{{translate('Generate')}}</button>

                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">


    function loaderOnClick() {
        $('#generate-custom-cards-modal-content').addClass('loader');
    }
    function closeModal(e) {
        $('#generate-custom-cards').modal('hide');
        setTimeout(function() {
            e.reset();
        }, 1500)
    }

</script>

