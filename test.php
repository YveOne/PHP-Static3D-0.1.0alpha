<?php

error_reporting(E_ALL);

define("S3D_BACKFACE", true);
define("S3D_DEBUG", true);
define("S3D_DEBUG_SYSTEMFONT", 2);
define("S3D_DEBUG_FONTHEIGHT", 13);
define("S3D_DEBUG_FRONTCOLOR", 0x00ffffff);
define("S3D_DEBUG_BACKCOLOR", 0x00222222);
define("S3D_MINDIST", 0.1);
define("S3D_MAXDIST", 0x00ffffff);

include("static3d/static3d.php");










$width = 1600;
$height = 700;
$bgColor = 0x00000000;

$camera = [-300,1800,600,1];
$target = [0,0,300,1];
$up = [0,0,1,1];

$fov = deg2rad(40); //53.130102354155
$aspect = $width/$height;
$near = 1;
$far = 100;

S3D::imageCreate($width, $height, $bgColor);
S3D::setView( Matrix4::lookAtLH($camera, $target, $up) );
S3D::setProjection( Matrix4::perspectiveFovLH($fov, $aspect, $near, $far) );
S3D::beginScene();

//--------------------------------------------------
// RENDER SCENE START




    $landscapeWidth = 1600;
    $landscapeHeight = 900;



    // draw ground lines

    S3D::setTexture( (imagecreatefromjpeg("ground.jpg")) );

    S3D::setWorld( Matrix4::translation(0, 0, 0) );

    $lineWidth = $landscapeWidth;
    $lineWidthHalf = $lineWidth / 2;
    $lineHeight = $landscapeWidth;
    $lineHeightHalf = $lineHeight / 2;
    $linesPerSide = 123;

    $stepValY = $lineHeight / $linesPerSide;
    $stepValX = $lineWidth / $linesPerSide;

    $verticesWhite = [];
    for($y=$lineHeightHalf; $y>=-$lineHeightHalf; $y-=$stepValY)
    {
        $verticesWhite[] = [[-$lineWidthHalf, $y, -1, 1], [0, 1/$lineHeight*($y+$lineHeightHalf)]];
        $verticesWhite[] = [[+$lineWidthHalf, $y, -1, 1], [1, 1/$lineHeight*($y+$lineHeightHalf)]];
    }
    for($x=-$lineWidthHalf; $x<=$lineWidthHalf; $x+=$stepValX)
    {
        $verticesWhite[] = [[$x, -$lineHeightHalf, -1, 1], [1/$lineWidth*($x+$lineWidthHalf), 0]];
        $verticesWhite[] = [[$x, +$lineHeightHalf, -1, 1], [1/$lineWidth*($x+$lineWidthHalf), 1]];
    }
    S3D::drawPrimitives($verticesWhite, S3D_LINELIST, S3D_TEXTURED, count($verticesWhite)/2, 0);




    // draw landscape images


    S3D::setTexture( (imagecreatefromjpeg("landscape.jpg")) );

    $textureW = $landscapeWidth;
    $textureH = $landscapeHeight;
    $verticesTriangle = [];
    $verticesTriangle[] = [[-$textureW/2, -$textureH/2, 0, 1], [0,0]];
    $verticesTriangle[] = [[ $textureW/2, -$textureH/2, 0, 1], [1,0]];
    $verticesTriangle[] = [[-$textureW/2,  $textureH/2, 0, 1], [0,1]];
    $verticesTriangle[] = [[ $textureW/2,  $textureH/2, 0, 1], [1,1]];

    $groundHalf = $landscapeWidth / 2;
    $landscapeHalf = $landscapeHeight / 2;



    S3D::setWorld(
        Matrix4::prependMatrix(
            Matrix4::rotationX(deg2rad(-90)),
            Matrix4::translation(0, -$groundHalf, $landscapeHalf)
        )
    );
    S3D::drawPrimitives($verticesTriangle, S3D_TRIANGLESTRIP, S3D_TEXTURED, 2, 0);

    S3D::setWorld(
        Matrix4::prependMatrix(
            Matrix4::prependMatrix(
                Matrix4::rotationX(deg2rad(-90)),
                Matrix4::rotationZ(deg2rad(90))
            ),
            Matrix4::translation(-$groundHalf, 0, $landscapeHalf)
        )
    );
    S3D::drawPrimitives($verticesTriangle, S3D_TRIANGLESTRIP, S3D_TEXTURED, 2, 0);


    S3D::setWorld(
        Matrix4::prependMatrix(
            Matrix4::prependMatrix(
                Matrix4::rotationX(deg2rad(-90)),
                Matrix4::rotationZ(deg2rad(-90))
            ),
            Matrix4::translation($groundHalf, 0, $landscapeHalf)
        )
    );
    S3D::drawPrimitives($verticesTriangle, S3D_TRIANGLESTRIP, S3D_TEXTURED, 2, 0);



// RENDER SCENE END
//--------------------------------------------------


// output

S3D::endScene();
S3D::imageAppend(imagecreatefrompng("watermark.png"), -10,-10, S3D_ANCHOR_BR);
S3D::finish();
S3D::display(S3D_PNG);
S3D::dispose();








