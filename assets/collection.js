function addCollectionAttributes() {

    const collectionHolder = document.querySelector('#custom-attributes-wrapper');

    const item = document.createElement('div');
    item.className = 'item';

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
    addRemoveCollectionAttributes(item)
}

function addRemoveCollectionAttributes(item) {
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Delete attributes';
    removeFormButton.href = "#"
    removeFormButton.setAttribute("style", "float: right");
    removeFormButton.setAttribute("class", "btn btn-secondary btn-sm");
    // removeFormButton.setAttribute("class", "mb-3");

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document
        .querySelector('#add-custom-attributes')
        .addEventListener('click', (e) => {
            e.preventDefault();

            addCollectionAttributes()
        })
    document
        .querySelectorAll('#custom-attributes-wrapper div.item')
        .forEach((row) => {
            addRemoveCollectionAttributes(row)
        })
})

