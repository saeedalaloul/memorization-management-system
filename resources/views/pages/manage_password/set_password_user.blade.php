<div class="col-md-6">
    <div class="form-row">
        <div class="col">
            <label for="inputPassword" style="font-size: 15px; color: #1e7e34">كلمة المرور*</label>
            <input type="password" class="form-control" name="password" wire:model.defer="password">
            @error('password')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="col">
            <label for="inputPassword_confirm" style="font-size: 15px; color: #1e7e34">تأكيد كلمة المرور*</label>
            <input type="password" wire:keydown.enter="setPasswordUser()" class="form-control" name="password_confirmation" wire:model.defer="password_confirmation">
            @error('password_confirmation')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <div class="form-row">
        <button wire:click.prevent="setPasswordUser()" class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                type="button">تعيين كلمة المرور
        </button>
    </div>
</div>
