/**
 * Make news text to wrap around news image nicely
 */

.news-article .news-image-block {
	margin: 10px 0 5px 0;
	color: grey;
	font-size: 0.8em;
	line-height: 1.3em;
}

.news-article .news-image-block img,
.elgg-news-preview img {
	width: 100%;
}

/**
 * Gallery view
 */
.elgg-gallery-news > li {
	width: 48%;
	vertical-align: top;
	margin: 10px 4% 40px 0;
}

.elgg-gallery-news li:nth-child(even) {
	margin-right: 0;
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

.news-gallery-item .subtitle,
.news-article .subtitle {
	display: inline-block;
	margin: 3px 5px 10px 3px;
	color: #000;
}

.news-gallery-item .elgg-icon,
.news-article .elgg-icon {
	font-size: 14px;
	color: #000;
}

.elgg-news-preview {
	background: white;
	margin-bottom: 30px;
}

.elgg-news-preview h2 {
	margin: 10px 0;
}

.elgg-news-preview .elgg-content {
	padding: 10px 20px 30px 20px;
}
