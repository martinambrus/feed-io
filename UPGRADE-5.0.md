# UPGRADE FROM 4.x to 5.0

## FeedIo\Feed\Item\MediaInterface::isThumbnail gets removed

This method was a misconception, as a consequence it got deprecated starting from 4.5 and is removed in 5.0. Don't use it anymore and take advantage of get/setThumbnail methods instead (see https://github.com/alexdebril/feed-io/pull/248 for more explanations).
