(function ($) {

    $.fn.collectionForm = function () {
        return this.each(function () {
            var $collectionHolder = $(this);
            if ($collectionHolder.data('collection')) {
                return;
            }

            $collectionHolder.data('collection', 'form');

            init($collectionHolder);
        });
    };

    function init($collectionHolder) {
        var addTitle = $collectionHolder.data("add-title") || "Add new";
        var $addTagLink = $('<a href="#" class="btn btn-sm btn-default add_tag_link"><i class="fa fa-plus"></i> '+addTitle+'</a>');
        var $newLinkContainer = $('<div></div>').append($addTagLink);

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkContainer);
        $collectionHolder.find('.collection-form-item').each(function() {
            addTagFormDeleteLink($(this));
        });

        $collectionHolder.data('index', $collectionHolder.find('.collection-form-item').length);

        $addTagLink.on('click', function (e) {
            e.preventDefault();

            addElement($collectionHolder, $newLinkContainer);
        });
    }

    function addElement($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $(newForm);
        $newLinkLi.before($newFormLi);

        addTagFormDeleteLink($newFormLi);
        return $newFormLi;
    }

    function addTagFormDeleteLink($tagFormLi) {
        var $removeFormA;
        var removeLinkContainer = $tagFormLi.find(".collection-form-remove-container");
        if (removeLinkContainer.length > 0) {
            $removeFormA = $('<a href="#" class="btn btn-danger"><i class="fa fa-times"></i></a>');
            removeLinkContainer.append($("<span class='input-group-btn'></span>").append($removeFormA));
        } else {
            $removeFormA = $('<a href="#" class="pull-right btn-xs btn btn-default" style="margin-top:-10px;margin-bottom:7px;"><i class="fa fa-times"></i> Pašalinti</a>');
            $tagFormLi.append($('<div class=""></div>').append($removeFormA));
        }

        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // remove the li for the tag form
            $tagFormLi.remove();
        });
    }

}(jQuery));
