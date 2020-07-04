<!-- .bs-modal-sm -->
<div class="modal fade bs-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        @if(View::hasSection('small_modal_form'))
            <form method="@yield('small_modal_method')" action="@yield('small_modal_url')" enctype="multipart/form-data">
                {{ csrf_field() }}
        @endif
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel2">@yield('small_modal_title')</h4>
                </div>
                <div class="modal-body">
                    @yield('small_modal_content')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ ucwords(lang('close', $translation)) }}</button>
                    @if(View::hasSection('small_modal_btn_label'))
						<button type="submit" class="btn btn-primary btn-submit" @if(View::hasSection('small_modal_btn_onclick')) onclick="@yield('small_modal_btn_onclick')"  @endif>
							@yield('small_modal_btn_label')
						</button>
					@endif
                </div>
            </div>
        @if(View::hasSection('small_modal_form'))
            </form>
        @endif
    </div>
</div>
<!-- /.bs-modal-sm -->