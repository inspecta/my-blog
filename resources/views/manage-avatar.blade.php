<x-layout>
    <h3 class="text-center">Upload your avatar!</h3>
    <form action="/upload-avatar" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="ml-5">
            <input type="file" name="avatar" required />
            @error('avatar')
                <p class="alert alert-danger shadow-sm">{{ $message }}</p>
            @enderror
        </div>
        <div class="ml-5">
            <button class="btn btn-md btn-primary my-3">Upload avatar</button>
        </div>
    </form>
</x-layout>
