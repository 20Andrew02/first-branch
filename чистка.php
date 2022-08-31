<?php
function stripTagsAttributes($html, $allowedTags = array(), $allowedAttributes = array('(?:a^)')) {
    if (!empty($html)) {
        $theTags = count($allowedTags) ? '<' . implode('><', $allowedTags) . '>' : '';
        $theAttributes = '%' . implode('|', $allowedAttributes) . '%i';
        $dom = @DOMDocument::loadHTML(
            mb_convert_encoding(
                strip_tags(
                    $html,
                    $theTags
                ),
                'HTML-ENTITIES',
                'UTF-8'
            )
        );
        $xpath = new DOMXPath($dom);
        $tags = $xpath->query('//*');
        foreach ($tags as $tag) {
            $attrs = array();
            for ($i = 0; $i < $tag->attributes->length; $i++) {
                $attrs[] = $tag->attributes->item($i)->name;
            } 
            foreach ($attrs as $attribute) {
                if (!preg_match($theAttributes, $attribute)) {
                    $tag->removeAttribute($attribute);
                } elseif (preg_match('%^(?:href|src)$%i', $attribute) and preg_match('%^javascript:%i', $tag->getAttribute($attribute))) {
                    $tag->setAttribute($attribute, '#');
                }
            }
        }
        return (
            trim(
                strip_tags(
                    html_entity_decode(
                        $dom->saveHTML()
                    ),
                    $theTags
                )
            )
        );
    }
}
?>