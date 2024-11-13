<script>
    $(function () {
        var myModalEl = document.getElementById('exampleModal');

        myModalEl.addEventListener('show.bs.modal', function (event) {
            // do something...
            var modalTitle = myModalEl.querySelector('.modal-body');
            axios.get('/admin/media')
                .then(function (response) {
                    // handle success
                    let x = `<div class='row'>`;
                    response.data.forEach(
                        (element) => {
                            x = x + `
<div class="col col-sm-2 mb-3">
<div class='card h-100'>
<div class="card-body card-img">
<img src="${element.imageUrl}" class="img-thumbnail w-100">
</div>
<div class='card-footer'>
${element.name}
</div>
</div>
</div>
`;
                            console.log(element);
                        }
                    );
                    x = x + `</div>`;
                    console.log(response);
                    console.log(myModalEl);
                    modalTitle.innerHTML = x;
                    console.log(x);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .finally(function () {
                    // always executed
                });

        })
    });
</script>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Select Image
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Select image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
