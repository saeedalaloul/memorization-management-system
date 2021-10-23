    @once
        <div id="datatable_wrapper"
             class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_length" id="perPage"><label>Show <select
                                name="perPage" aria-controls="datatable" wire:model="perPage"
                                class="form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> entries</label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div id="datatable_filter" class="dataTables_filter">
                        <label>Search:<input type="search"
                                             class="form-control form-control-sm"
                                             placeholder=""
                                             wire:model.debounce.300ms="search"
                                             aria-controls="datatable"></label>
                    </div>
                </div>
            </div>
        </div>
    @endonce
