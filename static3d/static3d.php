<?php




error_reporting(E_ALL);

include("inc/math.php");
include("inc/vector2.php");
include("inc/vector3.php");
include("inc/matrix4.php");
include("inc/line3.php");
include("inc/plane.php");
include("inc/intersection.php");
include("inc/boundingfrustum.php");

include("static3d_zbuffer.php");
include("static3d_surface.php");
include("static3d_SkinHandler.php");
include("static3d_clipping.php");

define("S3D_POINTS", 1);
define("S3D_LINELIST", 2);
define("S3D_LINESTRIP", 4);
define("S3D_TRIANGLELIST", 8);
define("S3D_TRIANGLESTRIP", 16);

define("S3D_COLORED", 1024);
define("S3D_TEXTURED", 2048);

define("S3D_PNG", 1);
define("S3D_JPG", 2);

define("S3D_STAGE_INITIALIZATION", 0);
define("S3D_STAGE_DRAWING", 1);
define("S3D_STAGE_POSTPROCESSING", 2);
define("S3D_STAGE_FINISHED", 3);
define("S3D_STAGE_DISPOSED", 4);

define("S3D_ANCHOR_TL", 0);
define("S3D_ANCHOR_TR", 1);
define("S3D_ANCHOR_BR", 2);
define("S3D_ANCHOR_BL", 3);

if(!defined("S3D_DEBUG")) define("S3D_DEBUG", false);
if(!defined("S3D_DEBUG_FRONTCOLOR")) define("S3D_DEBUG_FRONTCOLOR", 0x00ffffff);
if(!defined("S3D_DEBUG_BACKCOLOR")) define("S3D_DEBUG_BACKCOLOR", 0x00555555);
if(!defined("S3D_DEBUG_SYSTEMFONT")) define("S3D_DEBUG_SYSTEMFONT", 4);
if(!defined("S3D_DEBUG_FONTHEIGHT")) define("S3D_DEBUG_FONTHEIGHT", 13);
if(!defined("S3D_DEBUG_POSITIONX")) define("S3D_DEBUG_POSITIONX", 10);
if(!defined("S3D_DEBUG_POSITIONY")) define("S3D_DEBUG_POSITIONY", 10);

if(!defined("S3D_MINDIST")) define("S3D_MINDIST", 0.001);
if(!defined("S3D_MAXDIST")) define("S3D_MAXDIST", 0x00ffffff);
if(!defined("S3D_FILENAME")) define("S3D_FILENAME", "static3d");
if(!defined("S3D_BACKFACE")) define("S3D_BACKFACE", false);

    set_error_handler(function($errcode, $errstr, $errfile, $errfileline){
if(!S3D_DEBUG) return true;
        switch ($errcode)
        {
            // FATAL
            case E_ERROR:
            case E_USER_ERROR:
                $errcode = "ERROR";
                break;
            // WARNING
            case E_WARNING:
            case E_USER_WARNING:
                $errcode = "WARNING";
                break;
            // NOTICE
            case E_NOTICE:
            case E_USER_NOTICE:
                $errcode = "NOTICE";
                break;
            // UNKNOWN
            default:
                $errcode = "$errcode";
                break;
        }
        Static3D::writeLine("[$errcode] {$errstr} ({$errfile} : {$errfileline})");
        return true;
    });


// row majow based
// left handed
class Static3D
{
    private function __construct(){}

    static private $_stage = S3D_STAGE_INITIALIZATION;
    static private $_sceneTimeStart = 0;
    static private $_sceneTimeEnd = 0;
    static private $_systemStrings = [];

    static private $_mWorld = null;
    static private $_mView = null;
    static private $_mProjection = null;
    static private $_mWorldView = null;

    static private $_frustumNearX = 0;
    static private $_frustumNearY = 0;
    static private $_frustumNearZ = 0;
    static private $_frustumFarX = 0;
    static private $_frustumFarY = 0;
    static private $_frustumFarZ = 0;
    static private $_farMulWidth = 0;
    static private $_farMulHeight = 0;

    static private $_surface = null;
    static private $_zBuffer = null;

    static private $_width = 0;
    static private $_height = 0;
    static private $_widthHalf = 0;
    static private $_heightHalf = 0;
    static private $_zeroBasedWidth = 0;
    static private $_zeroBasedHeight = 0;
    static private $_zeroBasedWidthHalf = 0;
    static private $_zeroBasedHeightHalf = 0;


    static public function writeLine($text)
    {
        if(count(self::$_systemStrings) > 100) return;
        self::$_systemStrings[] = &$text;
    }


/////////////////////////////////////////////////
//REGION Constructor

    static public function imageCreate($width, $height, $bgColor=0x00000000)
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
        $res = @imagecreatetruecolor($width, $height);
        imagefill($res, 0, 0, $bgColor);
        self::$_surface = S3D_Surface::init($res);
    }

    static public function imageFromFile()
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
//TODO
    }

    static public function imageFromString()
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
//TODO
    }

    static public function imageFromResource($res)
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
        self::$_surface = S3D_Surface::init($res);
    }

//REGION Constructor
/////////////////////////////////////////////////
//REGION Matrices

    static public function setWorld($mWorld)
    {
        self::$_mWorld = &$mWorld;
        // mulitply world and view in one shot
        if(self::$_mView) self::$_mWorldView = Matrix4::prependMatrix(self::$_mWorld, self::$_mView);
    }

    static public function setView($mView)
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
        self::$_mView = &$mView;
    }

    static public function setProjection($mProjection)
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
        self::$_mProjection = &$mProjection;
    }

//REGION Matrices
/////////////////////////////////////////////////
//REGION Stages

    static public function beginScene()
    {
        if(self::$_stage != S3D_STAGE_INITIALIZATION) return;
        self::$_stage = S3D_STAGE_DRAWING;

        // only start scene when view and projection is set
        if(!self::$_mView) return false;
        if(!self::$_mProjection) return false;

        // world not set yet? set to identity
        if(!self::$_mWorld) self::$_mWorld = Matrix4::identity();

        // mulitply world and view in one shot
        self::$_mWorldView = Matrix4::prependMatrix(self::$_mWorld, self::$_mView);

        // bounding frustum / near and far plane values
        $bfc = BoundingFrustum::fromMatrix(self::$_mProjection)[1];
        self::$_frustumNearX = $bfc[2][0];
        self::$_frustumNearY = $bfc[2][1];
        self::$_frustumNearZ = $bfc[0][2];
        self::$_frustumFarX  = $bfc[6][0];
        self::$_frustumFarY  = $bfc[6][1];
        self::$_frustumFarZ  = $bfc[4][2];

        // surface sizes
        self::$_width  = S3D_Surface::width();
        self::$_height = S3D_Surface::height();
        self::$_widthHalf  = self::$_width * 0.5;
        self::$_heightHalf = self::$_height * 0.5;
        self::$_zeroBasedWidth  = self::$_width - 1;
        self::$_zeroBasedHeight = self::$_height - 1;
        self::$_zeroBasedWidthHalf  = self::$_zeroBasedWidth * 0.5;
        self::$_zeroBasedHeightHalf = self::$_zeroBasedHeight * 0.5;

        //  far plane values for z reconstruction
        self::$_farMulWidth  = 2 / self::$_zeroBasedWidth * self::$_frustumFarX;
        self::$_farMulHeight = 2 / self::$_zeroBasedHeight * self::$_frustumFarY;

        // zbuffer image
        self::$_zBuffer = S3D_zBuffer::init(self::$_width, self::$_height, S3D_MAXDIST);

        // fps
        self::$_sceneTimeStart = microtime(true);

        return true;
    }

    static public function endScene()
    {
        if(self::$_stage != S3D_STAGE_DRAWING) return;
        self::$_stage = S3D_STAGE_POSTPROCESSING;
    }

    static public function finish()
    {
        if(self::$_stage != S3D_STAGE_POSTPROCESSING) return;
        self::$_stage = S3D_STAGE_FINISHED;
        self::$_sceneTimeEnd = microtime(true);
        if(S3D_DEBUG)
        {
            $t = round(self::$_sceneTimeEnd - self::$_sceneTimeStart, 3);
            $fps = round(1/$t, 3);
            S3D_Surface::drawSystemString("rendertime: {$t} seconds");
            //S3D_Surface::drawSystemString("fps: {$fps} frames");
            if(self::$drawnPoints) S3D_Surface::drawSystemString("drawn points: ".self::$drawnPoints);
            if(self::$drawnLines) S3D_Surface::drawSystemString("drawn lines: ".self::$drawnLines);
            if(self::$drawnTriangles) S3D_Surface::drawSystemString("drawn triangles: ".self::$drawnTriangles);
            S3D_Surface::drawSystemString();


        }
        foreach(self::$_systemStrings as &$text)
            S3D_Surface::drawSystemString($text);
        self::$_systemStrings = [];
    }

    static public function display($type, $quality=null)
    {
        if(self::$_stage != S3D_STAGE_FINISHED) return;
        S3D_Surface::output($type, $quality, null);
    }

    static public function dispose()
    {
        if(self::$_stage != S3D_STAGE_FINISHED) return;
        self::$_stage = S3D_STAGE_DISPOSED;
        S3D_Surface::dispose();
        S3D_zBuffer::dispose();
        S3D_SkinHandler::dispose();
    }

//REGION Stages
/////////////////////////////////////////////////
//REGION Post Processing

    static public function imageAppend($res, $x, $y, $anchor=S3D_ANCHOR_TL)
    {
        if(self::$_stage != S3D_STAGE_POSTPROCESSING) return;
        if(!$res) return;
        S3D_Surface::overlay($res, $x, $y, $anchor);
    }

//REGION Post Processing
/////////////////////////////////////////////////
//REGION Drawing Primitives

    // homogen/w
    static public function space2clip($v)
    {
        return [
            $v[0] / $v[3],
            $v[1] / $v[3]
        ];
    }

    // viewport
    static public function clip2screen($v)
    {
        return [
            ($v[0]+1) * self::$_zeroBasedWidthHalf,
            (1- ($v[1]+1)*0.5) * self::$_zeroBasedHeight
        ];
    }

    static public function drawPrimitives(&$vertices, $drawTopology, $drawSkin, $count, $offset=0)
    {
        if(self::$_stage != S3D_STAGE_DRAWING) return;
        $countVertices = count($vertices);
        //$offset = $offset % $countVertices;
        if($offset < 0) $offset = ($offset%$countVertices)+$countVertices;

        switch($drawTopology)
        {
            case S3D_POINTS:
                $drawTopology = "Point";
                $countPrimitives = min($count, (int)(($countVertices-$offset)*1));
                $countVertices = 1;
                $shift = 0;
                $alias = [[0=>0]];
                break;
            case S3D_LINELIST:
                $drawTopology = "Line";
                $countPrimitives = min($count, (int)(($countVertices-$offset)*0.5));
                $countVertices = 2;
                $shift = 0;
                $alias = [[0=>0, 1=>1]];
                break;
            case S3D_LINESTRIP:
                $drawTopology = "Line";
                $countPrimitives = min($count, (int)($countVertices-$offset-1));
                $countVertices = 2;
                $shift = 1;
                $alias = [[0=>0, 1=>1]];
                break;
            case S3D_TRIANGLELIST:
                $drawTopology = "Triangle";
                $countPrimitives = min($count, (int)(($countVertices-$offset)/3));
                $countVertices = 3;
                $shift = 0;
                $alias = [[0=>0, 1=>1, 2=>2]];
                break;
            case S3D_TRIANGLESTRIP:
                $drawTopology = "Triangle";
                $countPrimitives = min($count, (int)($countVertices-$offset-2));
                $countVertices = 3;
                $shift = 2;
                $alias = [[0=>0, 1=>1, 2=>2], [0=>1, 1=>0, 2=>2]];
                break;
            default:
                // no topology set
                return;
        }

        switch($drawSkin)
        {
            case S3D_COLORED:
                $drawSkin = "Colored";
                break;
            case S3D_TEXTURED:
                $drawSkin = "Textured";
                break;
            default:
                // no skin type set
                return;
        }

        // nothing to draw
        if($countPrimitives <= 0) return;

        // drawPoint
        // drawLine
        // drawTriangle
        $drawFunction = "self::draw{$drawTopology}";

        // S3D_SkinHandler::initColoredPoint
        // S3D_SkinHandler::initColoredLine
        // S3D_SkinHandler::initColoredTriangle
        $skinFunction = "S3D_SkinHandler::init{$drawSkin}{$drawTopology}";

        $verticesI = $offset;
        $aliasC = count($alias);
        $aliasI = $offset%$aliasC;

        while($countPrimitives--)
        {
            $argsVertices = [];
            for($i=0; $i<$countVertices; $i++) $argsVertices[$alias[$aliasI][$i]] = &$vertices[$verticesI++];
            ksort($argsVertices);
            $aliasI = ($aliasI+1)%$aliasC;
            for($i=0; $i<$shift; $i++) $verticesI--;

            $argsTopo = [];
            $argsSkin = [];
            foreach($argsVertices as &$arg)
            {
                $argsTopo[] = &$arg[0];
                $argsSkin[] = &$arg[1];
            }
            unset($arg);

            $argsTopo[] = call_user_func_array("{$skinFunction}", $argsSkin);
            call_user_func_array("{$drawFunction}", $argsTopo);
        }
    }

    static public function setTexture($res)
    {
        S3D_SkinHandler::setTexture($res);
    }

//REGION Drawing Primitives
/////////////////////////////////////////////////
//REGION Drawing Points

static private $drawnPoints = 0;
static private $drawnLines = 0;
static private $drawnTriangles = 0;




    static private function drawPoint($point3d, $skinHandler)
    {
self::$drawnPoints++;
        // import global variables
        $_mWorldView = &self::$_mWorldView;
        $_mProjection = &self::$_mProjection;
        $_surface = &self::$_surface;
        $_zBuffer = &self::$_zBuffer;

        // model -> camera
        $point3d = Matrix4::multiplyVectorPost($_mWorldView, $point3d);
        $pointX = &$point3d[0];
        $pointY = &$point3d[1];
        $pointZ = &$point3d[2];
        if($pointZ < S3D_MINDIST) return;
        if($pointZ > S3D_MAXDIST) return;

        // camera -> ndc -> screen
        $point2d = self::space2clip((Matrix4::multiplyVectorPost($_mProjection, $point3d)));
        if($point2d[0] < -1 || $point2d[0] > 1) return;
        if($point2d[1] < -1 || $point2d[1] > 1) return;
        $point2d = self::clip2screen($point2d);

        $x = (int)$point2d[0];
        $y = (int)$point2d[1];
        $z = (int)$point3d[2];

        // handle zbuffer
        if(!$_zBuffer($x, $y, $z)) return false;

        // draw pixel
        $_surface($x, $y, $skinHandler());
    }

//REGION Drawing Points
/////////////////////////////////////////////////
//REGION Drawing Lines

    static private function drawLine($from3d, $to3d, $skinHandler)
    {
self::$drawnLines++;
        // import global variables
        $_mWorldView = &self::$_mWorldView;
        $_mProjection = &self::$_mProjection;
        $_frustumFarX = &self::$_frustumFarX;
        $_frustumFarY = &self::$_frustumFarY;
        $farZ = self::$_frustumFarZ - self::$_frustumNearZ;
        $_farMulWidth = &self::$_farMulWidth;
        $_farMulHeight = &self::$_farMulHeight;
        $_surface = &self::$_surface;
        $_zBuffer = &self::$_zBuffer;

        // model -> camera
        $from3d = Vector3::multiplyByMatrix($from3d, $_mWorldView);
        $to3d   = Vector3::multiplyByMatrix($to3d, $_mWorldView);
        if(!S3D_Clipping::lineZ($from3d, $to3d, S3D_MINDIST, S3D_MAXDIST)) return;
        $fromX = &$from3d[0];
        $fromY = &$from3d[1];
        $fromZ = &$from3d[2];
        $toX = &$to3d[0];
        $toY = &$to3d[1];
        $toZ = &$to3d[2];

        // camera -> ndc -> clip -> screen
        $from2d = self::space2clip((Vector3::multiplyByMatrix($from3d, $_mProjection)));
        $to2d   = self::space2clip((Vector3::multiplyByMatrix($to3d, $_mProjection)));
        if(!S3D_Clipping::line2($from2d, $to2d)) return;
        $from2d = self::clip2screen($from2d);
        $to2d = self::clip2screen($to2d);

$from2dX = ($from2d[0]-0.5);
$from2dY = ($from2d[1]-0.5);
$to2dX = ($to2d[0]-0.5);
$to2dY = ($to2d[1]-0.5);

        // original line in space for reconstructing z
        $spaceLineDirX = $toX-$fromX;
        $spaceLineDirY = $toY-$fromY;
        $spaceLineDirZ = $toZ-$fromZ;
        $spaceLineLength = sqrt($spaceLineDirX*$spaceLineDirX + $spaceLineDirY*$spaceLineDirY + $spaceLineDirZ*$spaceLineDirZ);

        // pre calc vars for screen drawing
        $drawDirX = $to2dX - $from2dX;
        $drawDirY = $to2dY - $from2dY;
        $drawParts = (int)(max(abs($drawDirX), abs($drawDirY)));
        if ($drawParts == 0) return; //TODO? draw point
        $drawAddX = $drawDirX / $drawParts;
        $drawAddY = $drawDirY / $drawParts;
        $drawCurX = $from2dX - $drawAddX;
        $drawCurY = $from2dY - $drawAddY;

        // drawloop
        for ($i=0; $i<=$drawParts; $i++)
        {
            $drawCurX += $drawAddX;
            $drawCurY += $drawAddY;

                // xy position on far plane
                $farX = ($drawCurX+0.5) * $_farMulWidth  - $_frustumFarX;
                $farY = ($drawCurY+0.5) * $_farMulHeight - $_frustumFarY;

            // create test line from 0/0/0 to far plane
            // to find intersection with space line
            $lrX = $farY*$spaceLineDirZ - $spaceLineDirY*$farZ;
            $lrY = $farZ*$spaceLineDirX - $spaceLineDirZ*$farX;
            $lrZ = $farX*$spaceLineDirY - $spaceLineDirX*$farY;
            $aX = $farY*-$fromZ - -$fromY*$farZ;
            $aY = $farZ*-$fromX - -$fromZ*$farX;
            $aZ = $farX*-$fromY - -$fromX*$farY;
            $doneDist = ($aX*$lrX + $aY*$lrY + $aZ*$lrZ) / ($lrX*$lrX + $lrY*$lrY + $lrZ*$lrZ);
//if($doneDist > 1 || $doneDist < 0) continue;
            $nextDist = 1 - $doneDist;

            // intersection point
            //$x = $doneDist*$spaceLineDirX + $fromX;
            //$y = $doneDist*$spaceLineDirY + $fromY;
            $z = (int)($doneDist*$spaceLineDirZ + $fromZ);
            $x = (int)($drawCurX);
            $y = (int)($drawCurY);

            // handle zbuffer
            if(!$_zBuffer($x, $y, $z)) continue;

            // draw pixel
            $_surface($x, $y, $skinHandler($nextDist, $doneDist));
        }
    }

//REGION Drawing Lines
/////////////////////////////////////////////////
//REGION Drawing Triangles

    static private function drawTriangle($triangle1, $triangle2, $triangle3, $skinHandler)
    {
self::$drawnTriangles++;

        // import global variables
        $_mWorldView = &self::$_mWorldView;
        $_mProjection = &self::$_mProjection;
        $_frustumFarX = &self::$_frustumFarX;
        $_frustumFarY = &self::$_frustumFarY;
        $farZ = self::$_frustumFarZ - self::$_frustumNearZ;
        $_farMulWidth = &self::$_farMulWidth;
        $_farMulHeight = &self::$_farMulHeight;
        $_surface = &self::$_surface;
        $_zBuffer = &self::$_zBuffer;

        // model -> camera
        $triangle1 = Vector3::multiplyByMatrix($triangle1, $_mWorldView);
        $triangle2 = Vector3::multiplyByMatrix($triangle2, $_mWorldView);
        $triangle3 = Vector3::multiplyByMatrix($triangle3, $_mWorldView);


        // triangle polygon
        $poly = [
            $triangle1,
            $triangle2,
            $triangle3
        ];

        // clip minz/maxz
        if(!S3D_Clipping::polyZ($poly, S3D_MINDIST, S3D_MAXDIST)) return;

        // camera -> ndc -> clip -> screen
        foreach($poly as &$p)
            $p = self::space2clip(Vector3::multiplyByMatrix($p, $_mProjection));
        unset($p);
        if(!S3D_Clipping::poly2($poly)) return;
        foreach($poly as &$p)
            $p = self::clip2screen($p);
        unset($p);

        // variable pointers of triangle coords
        $triangle1x = &$triangle1[0];
        $triangle1y = &$triangle1[1];
        $triangle1z = &$triangle1[2];
        $triangle2x = &$triangle2[0];
        $triangle2y = &$triangle2[1];
        $triangle2z = &$triangle2[2];
        $triangle3x = &$triangle3[0];
        $triangle3y = &$triangle3[1];
        $triangle3z = &$triangle3[2];

        // original plane normal for z reconstruction
        // i am not normalizing the plane normal
        // because the intersection line wont get normalizes eiter
        // beeeecause its faster
        $spacePlaneN = Vector3::cross(
                (Vector3::subtract($triangle2, $triangle1)),
                (Vector3::subtract($triangle3, $triangle1))
        );
        $spacePlaneD = -Vector3::dot($spacePlaneN, $triangle1);

        // wait, before we continue
        // check if triangle is shown from behind
        if($spacePlaneD < -1e-6)
        {
            if(!S3D_BACKFACE) return;
            else $poly = array_reverse($poly);
        }
        elseif($spacePlaneD > 1e-6)
        {
        }
        // too close to zero
        // means the triangle is shown from side
        else return;

        // ok continue with plane normal variables
        // used for barycentric coordinates
        $spacePlaneNX = $spacePlaneN[0];
        $spacePlaneNY = $spacePlaneN[1];
        $spacePlaneNZ = $spacePlaneN[2];
        $spacePlaneArea2 = Vector3::length((Vector3::cross((Vector3::subtract($triangle2, $triangle1)), (Vector3::subtract($triangle3, $triangle1)))));
        $spacePlaneEdge1x = $triangle2x - $triangle1x;
        $spacePlaneEdge1y = $triangle2y - $triangle1y;
        $spacePlaneEdge1z = $triangle2z - $triangle1z;
        $spacePlaneEdge2x = $triangle3x - $triangle2x;
        $spacePlaneEdge2y = $triangle3y - $triangle2y;
        $spacePlaneEdge2z = $triangle3z - $triangle2z;
        $spacePlaneEdge3x = $triangle1x - $triangle3x;
        $spacePlaneEdge3y = $triangle1y - $triangle3y;
        $spacePlaneEdge3z = $triangle1z - $triangle3z;


        // pre calc vars for screen drawing loop
        $firstIndex = -1;
        $curY = 999999;
        $maxY = -1;
        $polySize = count($poly);
        foreach($poly as $i => &$p)
        {
            $p = [
                (int)$p[0],
                (int)$p[1]
            ];
            if($p[1] > $maxY) $maxY = $p[1];
            if($p[1] < $curY)
            {
                $curY = $p[1];
                $firstIndex = $i;
            }
        }
        unset($p);
        $firstPoint = $poly[$firstIndex];
        $lIndex = $firstIndex;
        $rIndex = $firstIndex;

        // the algoythm goes on both sides from point to point
        // on startup both points are the same
        $lPoint = $firstPoint;
        $rPoint = $firstPoint;
        $lPointLast = null;
        $rPointLast = null;

        // this is the height2do
        // on startup its just zero
        // let the algorythm find start values
        $lHeight = 0;
        $rHeight = 0;

        // lDrawX = from x position
        // rDrawX = to x position
        // at startup theyre both at x pos of toppest point
        $lDrawX = $firstPoint[0];
        $rDrawX = $firstPoint[0];
        $lDrawAddX = 0;
        $rDrawAddX = 0;

        // while we are drawing y rows
        while($curY <= $maxY)
        {

            // height2do on left
            if($lHeight == 0)
            {
                // decrease left index for next left point
                $lPointLast = $lPoint;
                $lIndex = ($lIndex-1+$polySize) % $polySize;
                $lPoint = $poly[$lIndex];
                // calculate new height2do and x-from change for each y row
                $lHeight = $lPoint[1] - $lPointLast[1] +1;
                if($lHeight == 0) continue;
                $lDrawAddX = ($lPoint[0] - $lPointLast[0]) / $lHeight;
                $lDrawX = $lPointLast[0];
            }
            // height2do on right
            if($rHeight == 0)
            {
                // increase right index for next right point
                $rPointLast = $rPoint;
                $rIndex = ($rIndex+1) % $polySize;
                $rPoint = $poly[$rIndex];
                // calculate new height2do and x-to change for each y row
                $rHeight = $rPoint[1] - $rPointLast[1] +1;
                if($rHeight == 0) continue;
                $rDrawAddX = ($rPoint[0] - $rPointLast[0]) / $rHeight;
                $rDrawX = $rPointLast[0];
            }

            // draw the current y row
            for($curX=$lDrawX; $curX<=$rDrawX; ++$curX)
            {

                // xy position on far plane
                //$farX = (int)($curX+0.5) * $_farMulWidth  - $_frustumFarX;
                //$farY = (int)($curY+0.5) * $_farMulHeight - $_frustumFarY;
                $farX = $curX * $_farMulWidth  - $_frustumFarX;
                $farY = $curY * $_farMulHeight - $_frustumFarY;

                // calc intersection point line/plane
                // (as i said... use unnormalized far ray)
                $interD = $spacePlaneD / -($spacePlaneNX*$farX + $spacePlaneNY*$farY + $spacePlaneNZ*$farZ);
                $interX = $farX * $interD;
                $interY = $farY * $interD;
                $interZ = $farZ * $interD;

                // handle zbuffer
                if(!$_zBuffer($curX, $curY, $interZ)) continue;

                // barycentric coordinates for skin
                //
                $_vpx = $interX - $triangle2x;
                $_vpy = $interY - $triangle2y;
                $_vpz = $interZ - $triangle2z;
                $_cX = $spacePlaneEdge2y*$_vpz - $_vpy*$spacePlaneEdge2z;
                $_cY = $spacePlaneEdge2z*$_vpx - $_vpz*$spacePlaneEdge2x;
                $_cZ = $spacePlaneEdge2x*$_vpy - $_vpx*$spacePlaneEdge2y;
                $dist1 = sqrt($_cX*$_cX + $_cY*$_cY + $_cZ*$_cZ) / $spacePlaneArea2;
                //
                $_vpx = $interX - $triangle3x;
                $_vpy = $interY - $triangle3y;
                $_vpz = $interZ - $triangle3z;
                $_cX = $spacePlaneEdge3y*$_vpz - $_vpy*$spacePlaneEdge3z;
                $_cY = $spacePlaneEdge3z*$_vpx - $_vpz*$spacePlaneEdge3x;
                $_cZ = $spacePlaneEdge3x*$_vpy - $_vpx*$spacePlaneEdge3y;
                $dist2 = sqrt($_cX*$_cX + $_cY*$_cY + $_cZ*$_cZ) / $spacePlaneArea2;
                //
                //$_vpx = $interX - $triangle1x;
                //$_vpy = $interY - $triangle1y;
                //$_vpz = $interZ - $triangle1z;
                //$_cX = $spacePlaneEdge1y*$_vpz - $_vpy*$spacePlaneEdge1z;
                //$_cY = $spacePlaneEdge1z*$_vpx - $_vpz*$spacePlaneEdge1x;
                //$_cZ = $spacePlaneEdge1x*$_vpy - $_vpx*$spacePlaneEdge1y;
                //$dist3 = sqrt($_cX*$_cX + $_cY*$_cY + $_cZ*$_cZ) / $spacePlaneArea2;
                $dist3 = 1 - $dist1 - $dist2;

                if($dist1 < 0) $dist1 = 0;
                else if($dist1 > 1) $dist1 = 1;
                if($dist2 < 0) $dist2 = 0;
                else if($dist2 > 1) $dist2 = 1;
                if($dist3 < 0) $dist3 = 0;
                else if($dist3 > 1) $dist3 = 1;

                $dist123 = $dist1 + $dist2 + $dist3;
                $dist1 /= $dist123;
                $dist2 /= $dist123;
                $dist3 /= $dist123;

                // draw pixel
                $_surface($curX, $curY, $skinHandler($dist1, $dist2, $dist3));
            }

            // increase x-from and x-to
            $lDrawX += $lDrawAddX;
            $rDrawX += $rDrawAddX;
            // next y row
            ++$curY;
            // decrease height2do
            --$lHeight;
            --$rHeight;
        }
    }

//REGION Drawing Triangles
/////////////////////////////////////////////////

}

class_alias("Static3D", "S3D");
