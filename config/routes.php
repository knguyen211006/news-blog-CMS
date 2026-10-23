<?php
use App\Controllers\{AuthController,PublicController,PostController,AdminController,CommentController};
return [
 ''=>['GET'=>[PublicController::class,'home']],
 'articles'=>['GET'=>[PublicController::class,'articles']],
 'article'=>['GET'=>[PublicController::class,'article']],
 'login'=>['GET'=>[AuthController::class,'login'],'POST'=>[AuthController::class,'login']],
 'register'=>['GET'=>[AuthController::class,'register'],'POST'=>[AuthController::class,'register']],
 'logout'=>['POST'=>[AuthController::class,'logout']],
 'profile'=>['GET'=>[AuthController::class,'profile'],'POST'=>[AuthController::class,'profile']],
 'author/posts'=>['GET'=>[PostController::class,'listing']],
 'author/edit'=>['GET'=>[PostController::class,'edit'],'POST'=>[PostController::class,'edit']],
 'author/preview'=>['GET'=>[PostController::class,'preview']],
 'author/action'=>['POST'=>[PostController::class,'action']],
 'comments/create'=>['POST'=>[CommentController::class,'create']],
 'comments/hide'=>['POST'=>[CommentController::class,'hide']],
 'admin'=>['GET'=>[AdminController::class,'dashboard']],
 'admin/posts'=>['GET'=>[PostController::class,'adminListing']],
 'admin/users'=>['GET'=>[AdminController::class,'users'],'POST'=>[AdminController::class,'users']],
 'admin/taxonomy'=>['GET'=>[AdminController::class,'taxonomy'],'POST'=>[AdminController::class,'taxonomy']],
 'admin/comments'=>['GET'=>[AdminController::class,'comments'],'POST'=>[AdminController::class,'comments']],
];
