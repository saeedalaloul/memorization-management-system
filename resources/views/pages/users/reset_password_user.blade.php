<div class="col-md-6">
    <div class="form-row">
        <div class="col">
            <label for="title" style="font-size: 15px; color: #1e7e34">اسم المستخدم</label>
            <input type="text" name="name" class="form-control" wire:model="name" readonly>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <div class="form-row">
        <div class="col">
            <label for="inputPassword" style="font-size: 15px; color: #1e7e34">كلمة المرور الجديدة*</label>
            <input type="password" class="form-control" name="password" wire:model="password">
            @error('password')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <label for="inputPassword_confirm" style="font-size: 15px; color: #1e7e34">تأكيد كلمة المرور*</label>
            <input type="password" class="form-control" name="password_confirm" wire:model="password_confirm">
            @error('password_confirm')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <button wire:click.prevent="resetPasswordUser()"
            class="btn btn-success btn-sm nextBtn btn-lg pull-right" type="button">إعادة تعيين كلمة المرور
    </button>
</div>
