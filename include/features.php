<?php
	function main_features() {
		global $feature_data;
		write_feature_data($feature_data);
	}
	
	function write_feature_data($data) {
		$first = true;
		$categories = array_keys($data);

		foreach($categories as $i => $key) {
			$category = $data[$key];
			$next = $i >= (sizeof($categories) - 1) ? "" : $categories[$i + 1];
			write_category($key, $category, $first, $next);
			$first = false;
		}
	}

	function write_category($name, $category, $displayed, $next) {
		$class = 'feature-category';
		if($displayed)
			$class = "$class active-category";

		div($class, array('data-category' => $name ));
			div('section-header');
				write(ucfirst($name));
			pop();
			$count = sizeof($category);

			$hide_count = false;
			if($count > 0) {
				$first = $category[0];
				if(array_key_exists('addon', $first) && $first['addon'])
					$count--;

				if(array_key_exists('hide_count', $first) && $first['hide_count'])
					$hide_count = true;
			}

			if(!$hide_count) {
				div('feature-count');
					write("(");
						span("feature-count-num");
							write($count);
						pop();
					write(" Features)");
				pop();
			}

			div('ad-container ad-needs-loading');
			pop();

			if($count == 0) {
				push('h1');
					write('No features here yet!');
				pop();
			} else {
				div('feature-list');
					usort($category, "cmp_features");
					foreach($category as $k => $feature)
						write_feature($feature, $name);
				pop();
			}

			if(strlen($next)) {
				div('data-category-changer std-button button-long button-next button-features', array('data-category' => $next));
					div('button-title');
						write('Next: ' . ucfirst($next));
					pop();
				pop();	
			} else {
				div('data-entry-changer std-button button-long button-next button-features', array('data-entry' => 'download'));
					div('button-title');
						write('Download Quark');
					pop();
				pop();	
			}
		pop();
	}

	function write_feature($feature, $category_name) {
		if(array_key_exists('info', $feature)) {
			div('info');
				write($feature['info']);
			pop();
			return;
		}

		$added_ver = array_key_exists('added', $feature) ? $feature['added'] : "1.14";
		$removed_ver = array_key_exists('removed', $feature) ? $feature['removed'] : '';
		$data = $removed_ver ? array('data-added' => $added_ver, 'data-removed' => $removed_ver) : array('data-added' => $added_ver);

		$class = 'feature';
		if($removed_ver)
			$class .= ' feature-removed';

		div($class, $data);
			div('feature-image');
				if (array_key_exists('title', $feature)) $title = $feature['title'];
				else $title = "";
				img("img/features/$category_name/{$feature['image']}","",$title);
			pop();

			div('feature-info');
				div('feature-header');
					div('feature-title');
						write($feature['name']);
					pop();

					div('feature-version');
						if(array_key_exists('custom_version', $feature))
							write($feature['custom_version']);
						else {
							if($removed_ver)
								write("[$added_ver, $removed_ver".'[');
							else 
								write("$added_ver+");
						}
					pop();
				pop();

				$has_expand = array_key_exists('expand', $feature);

				div('feature-desc' . ($has_expand ? ' feature-desc-with-expand' : ''));
					write_desc($feature['desc']);
				pop();

				if($has_expand) {
					div('feature-expand');
						write_desc($feature['expand']);
					pop();

					div('std-button feature-expand-button');
						div('button-title');
							write('More Info');
						pop();
					pop();
				} else {
					$has_download = array_key_exists('download', $feature);

					if($has_download) {
						a($feature['download'], 'no-external friend-dl-container');
							div('std-button button-download button-download-friend');
								div('button-title');
									write('Download ');
								pop();
							pop();
						pop();
					}
				}
			pop();
		pop();
	}

	function write_desc($arr) {
		$list = false;
		foreach($arr as $i => $line) {
			$fchar = $line[0];
			$rem = substr($line, 1);

			if($fchar == '*') {
				if(!$list)
					push('ul');

				$list = true;
				push('li');
					write($rem);
				pop();
			} else {
				if($list) {
					pop();
					$list = false;
				}

				switch ($fchar) {
					case '#':
						div('feature-desc-header');
							write($rem);
						pop();
						break;
					case '!':
						img($rem);
						break;
					case '-':
						write('<hr>');
						break;
					default:
						p($line);
						break;
				}
			}
		}

		if($list)
			pop();
	}

	function cmp_features($f1, $f2) {
		if((array_key_exists('addon', $f1) && $f1['addon']))
			return -1;	
		if((array_key_exists('addon', $f2) && $f2['addon']))
			return 1;	

		return strcmp($f1['name'], $f2['name']);
	}

	main_features();
?>