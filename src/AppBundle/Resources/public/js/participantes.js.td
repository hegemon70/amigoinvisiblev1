var $collectionHolder;

// setup an "add a participante" link
var $addParticipanteLink = $('<a href="#" class="add_participante_link">Add a participante</a>');
var $newLinkTd = $('<td></td>').append($addParticipanteLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of participantes
    $collectionHolder = $('tr.participantes');

    // add the "add a participante" anchor and li to the participantes ul
    $collectionHolder.append($newLinkTd);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addParticipanteLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new participante form (see next code block)
        addParticipanteForm($collectionHolder, $newLinkTd);
    });
});

function addParticipanteForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a participante" link li
    var $newFormTd = $('<td></td>').append(newForm);
    $newLinkTd.before($newFormTd);
}