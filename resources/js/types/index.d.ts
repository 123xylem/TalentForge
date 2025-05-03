import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    availableSkills?: Skill[];
    availableCategories?: Category[];
    userSkills?: Skill[];
    userCategories?: Category[];
    listings?: Listing[];
    flash?: {
        errors?: any;
        success?: any;
    };
    listing?: Listing;
    paginatedListingData?: PaginatedListingData;
    listingApplications?: ListingApplication[];
    listingApplication?: ListingApplication;
}

export interface PaginatedListingData {
    current_page: number;
    data: Listing[];
    first_page_url: string;
    from: number;
    last_page: 2;
    last_page_url: string;
    links: {
        url: string;
        label: string;
        active: boolean;
    }[];
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: null;
    to: number;
    total: number;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    type?: string;
    company?: string;
    bio?: string;
    website?: string;
    profile_image?: File | string | null;
    location?: string;
    cv?: File | string | null;
    phone?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    skills?: Skill[];
}

export interface Listing {
    id: number;
    title: string;
    description: string;
    salary?: string;
    location?: string;
    created_at?: string;
    updated_at?: string;
    skills?: Skill[];
    categories?: Category[];
    user_id?: number;
    company?: string;
    slug?: string;
    listingClosed: boolean;
}
export interface ListingApplication {
    id: number;
    user_id: number;
    cv: string;
    cover_letter: string;
    status: string;
    created_at: string;
    updated_at: string;
    listing: Listing;
    name?: string;
    email?: string;
    phone?: string;
    address?: string;
    applicant?: User;
}
export interface Skill {
    id: number;
    name: string;
}

export interface Category {
    id: number;
    name: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface NotificationsLinks {
    url: string | null;
    label?: string;
    active?: boolean;
}

export interface Notification {
    id: string;
    type: string;
    read_at: string | null;
    created_at: string;
    updated_at: string;
    notifiable_id: number;
    data: NotificationData;
}
