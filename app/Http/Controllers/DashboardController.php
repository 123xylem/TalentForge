<?php

namespace App\Http\Controllers;


use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
