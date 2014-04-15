<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Acme\DemoBundle\Form\ContactType;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//agrego libreria de google maps
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Events\MouseEvent;
use Ivory\GoogleMap\Overlays\Polyline;
use Ivory\GoogleMap\Controls\ControlPosition;
use Ivory\GoogleMap\Controls\MapTypeControl;
use Ivory\GoogleMap\Controls\MapTypeControlStyle;
use Ivory\GoogleMap\Controls\OverviewMapControl;
use Ivory\GoogleMap\Controls\PanControl;
use Ivory\GoogleMap\Controls\RotateControl;
use Ivory\GoogleMap\Controls\ScaleControlStyle;
use Ivory\GoogleMap\Controls\ScaleControl;
use Ivory\GoogleMap\Controls\ZoomControl;
use Ivory\GoogleMap\Controls\ZoomControlStyle;

class DemoController extends Controller {

    /**
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction() {

        $map = new Map();
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);
        $map->setAutoZoom(false);
        $map->setCenter(0, 0, true);
        $map->setMapOption('zoom', 3);
        $map->setBound(2.1, 3.9, 2.6, 1.4, true, true);
        $map->setMapOption('mapTypeId', MapTypeId::TERRAIN);
        $map->setMapOptions(array(
            'disableDefaultUI' => true,
            'disableDoubleClickZoom' => true,
        ));
        $map->setStylesheetOptions(array(
            'width' => '1200px',
            'height' => '500px',
        ));
        $map->setLanguage('es');


        //el apuntador
        $marker = new Marker();
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition(10, 10, true);
        $marker->setAnimation(Animation::BOUNCE);
        $marker->setOptions(array(
            'clickable' => true,
            'flat' => true,
        ));
        $map->addMarker($marker);


        $infoWindow = new InfoWindow();
        $infoWindow->setPrefixJavascriptVariable('info_window_');
        $infoWindow->setPosition(0, 0, true);
        $infoWindow->setPixelOffset(5, 5, 'px', 'pt');
        $infoWindow->setContent('<p>Aquí estoy</p>');
        $infoWindow->setOpen(false);
        $infoWindow->setAutoOpen(true);
        $infoWindow->setOpenEvent(MouseEvent::MOUSEOVER);
        $infoWindow->setAutoClose(true);
        $infoWindow->setOptions(array(
            'disableAutoPan' => false,
            'zIndex' => 20,
        ));

        $mapTypeControl = new MapTypeControl();
        $mapTypeControl->setMapTypeControlStyle(MapTypeControlStyle::HORIZONTAL_BAR);
        $mapTypeControl->setControlPosition(ControlPosition::RIGHT_TOP);
        $mapTypeControl->setMapTypeIds(array(MapTypeId::ROADMAP, MapTypeId::SATELLITE, MapTypeId::HYBRID, MapTypeId::TERRAIN));

        $overviewMapControl = new OverviewMapControl();
        $overviewMapControl->setOpened(true);

        $panControl = new PanControl();
        $panControl->setControlPosition(ControlPosition::LEFT_TOP);

        $rotateControl = new RotateControl();
        $rotateControl->setControlPosition(ControlPosition::LEFT_CENTER);


        $scaleControl = new ScaleControl();
        $scaleControl->setControlPosition(ControlPosition::BOTTOM_LEFT);
        $scaleControl->setScaleControlStyle(ScaleControlStyle::DEFAULT_);

        $zoomControl = new ZoomControl();
        $zoomControl->setControlPosition(ControlPosition::LEFT_CENTER);
        $zoomControl->setZoomControlStyle(ZoomControlStyle::LARGE);

// Add your pan control to the map
        $map->setZoomControl($zoomControl);
        $map->setScaleControl($scaleControl);
        $map->setRotateControl($rotateControl);
        $map->setPanControl($panControl);
        $map->setOverviewMapControl($overviewMapControl);
        $map->setMapTypeControl($mapTypeControl);
        $map->addInfoWindow($infoWindow);
        $marker->setInfoWindow($infoWindow);
        return array('map' => $map);
    }

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name) {
        return array('name' => $name);
    }

    /**
     * @Route("/contact", name="_demo_contact")
     * @Template()
     */
    public function contactAction() {
        $form = $this->get('form.factory')->create(new ContactType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $mailer = $this->get('mailer');
                // .. setup a message and send it
                // http://symfony.com/doc/current/cookbook/email.html

                $this->get('session')->getFlashBag()->set('notice', 'Message sent!');

                return new RedirectResponse($this->generateUrl('_demo'));
            }
        }

        return array('form' => $form->createView());
    }

}
