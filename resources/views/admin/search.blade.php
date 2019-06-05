<style>

    .search-forms {
        width: 250px;
        /*margin: 10px 0 0 20px;*/
        border-radius: 3px;
        margin-right:4px;
        float: right;
    }
    .search-forms input[type="text"] {
        color: #666;
        border: 1px solid black;
    }

    .search-forms .btn {
        color: white;
        background-color: #55acee;
        border: 0;
    }

</style>
<form action="{{ url()->current() }}" method="get" class="search-forms" pjax-container>
    <div class="input-group input-group-sm ">
        <input type="text" name="key" class="form-control" placeholder="输入Key..." value="{{ $key }}">
        <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
    </div>
</form>