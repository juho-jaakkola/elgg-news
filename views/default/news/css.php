/**
 * Make news text to wrap around news image nicely
 */
.news-article .news-image-block {
	max-width: 400px;
	float: right;
	margin: 10px 0 5px 10px;
	color: grey;
	font-size: 0.8em;
	line-height: 1.3em;
}

/**
 * Gallery view
 */
.elgg-gallery-news {
}

.elgg-gallery-news > li {
	width: 48%;
	vertical-align: top;
	margin: 10px 4% 10px 0;
}

.elgg-gallery-news li:nth-child(even) {
	margin-right: 0;
}

.news-gallery-item .subtitle {
	font-size: 11px;
}
.news-gallery-item img {
	float: left;
	margin: 0 0 10px 0;

	width: 100%;
	max-height: 200px;
	background-size: cover;
	background-repeat: no-repeat;
	background-position: center;
}
