class AdminSlideshow{

    constructor(el){
        this._model = el
        this._populateElements()
        this._renderModel()
        this._addListener()
    }

    _addListener(){
        $(this._el.adder).on('click', e => {
            let opts = {
                accept   : 'image/*',
                multiple : true,
                form     : 'std-image'
            }

            window['bootstrap-plugins'].Admin.prototype.pickFile(files => {
                files.forEach(file => this._renderItem({
                    url   : file.url,
                    title : '',
                    text  : '',
                    action: ''
                }))
            }, opts)
        })

        $(document.body).on('click', '.s-list-thumb', e => {
            window['bootstrap-plugins'].Admin.prototype.viewImage(e.target.href)
            return false
        })

        $(document.body).on('click', '.slide-remover', e => {
            let parent = $(e.target).closest('.admin-slideshow-item')
            parent.slideUp(() => parent.remove())
        })

        $('#s-form').on('submit', e => {
            let items  = document.querySelectorAll('.admin-slideshow-item')
            let result = []

            items.forEach(res => {
                let url    = $(res).find('a').attr('href')
                let text   = $(res).find('[data-slide="text"]').val()
                let title  = $(res).find('[data-slide="title"]').val()
                let action = $(res).find('[data-slide="action"]').val()

                result.push({url,text,title,action})
            })

            this._model.value = JSON.stringify(result)
        })
    }

    _hs(text){
        return text
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
    }

    _populateElements(){
        this._el = {
            adder : document.querySelector( this._model.dataset.adder ),
            editor: document.querySelector( this._model.dataset.editor )
        }
    }

    _renderItem(item){
        let safe = {
            url   : this._hs(item.url),
            title : this._hs(item.title),
            text  : this._hs(item.text),
            action: this._hs(item.action)
        }

        let html = `
            <div class="admin-slideshow-item">
                <div class="media">
                    <a href="${safe.url}" class="mr-3 s-list-thumb" style="background-image:url('${safe.url}')"></a>
                    <div class="media-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <textarea class="form-control form-control-sm" data-slide="text" placeholder="Text">${safe.text}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-sm" data-slide="title" placeholder="Title" value="${safe.title}">
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="url" class="form-control form-control-sm" data-slide="action" placeholder="Link" value="${safe.action}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-danger btn-sm btn-block slide-remover" type="button">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mb-3 mt-0">
            </div>`
        $(this._el.editor).append(html)
    }

    _renderModel(){
        let val = this._model.value
        if(!val)
            val = '[]'

        val = JSON.parse(val)
        if(!val)
            return;

        val.forEach(item => this._renderItem(item))
    }
}

$(() => {
    document.querySelectorAll('.admin-slideshow')
    .forEach(e => new AdminSlideshow(e))
})