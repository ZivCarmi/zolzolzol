<?php defined('ABSPATH') || exit;

$timber_context = $GLOBALS['timberContext'];

if (!isset($timber_context)) throw new \Exception('Timber context not set in footer.');

$timber_context['_content'] = ob_get_contents();

ob_end_clean();

Timber::render('plugin.twig', $timber_context);