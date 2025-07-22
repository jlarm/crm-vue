<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem, 
    DropdownMenuTrigger,
    DropdownMenuSeparator,
    DropdownMenuLabel
} from '@/components/ui/dropdown-menu';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { Search, Filter, MoreHorizontal, Phone, MapPin, Star, Building2 } from 'lucide-vue-next';

interface Dealership {
    id: number;
    name: string;
    address?: string;
    city?: string;
    state?: string;
    zip_code?: string;
    phone?: string;
    current_solution_name?: string;
    current_solution_use?: string;
    notes?: string;
    status: 'active' | 'inactive' | 'imported';
    rating: 'hot' | 'warm' | 'cold';
    type: string;
    in_development: boolean;
    dev_status: 'not_started' | 'in_progress' | 'completed' | 'on_hold';
    created_at: string;
    updated_at: string;
}

interface Props {
    dealerships: Dealership[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Dealerships',
        href: '/dealerships',
    },
];

// Reactive data
const searchQuery = ref('');
const statusFilter = ref<string | null>(null);
const ratingFilter = ref<string | null>(null);
const typeFilter = ref<string | null>(null);
const currentPage = ref(1);
const itemsPerPage = ref(25);

// Computed properties
const filteredDealerships = computed(() => {
    let filtered = props.dealerships;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(dealership => 
            dealership.name?.toLowerCase().includes(query) ||
            dealership.city?.toLowerCase().includes(query) ||
            dealership.state?.toLowerCase().includes(query) ||
            dealership.phone?.includes(query)
        );
    }

    // Status filter
    if (statusFilter.value) {
        filtered = filtered.filter(dealership => dealership.status === statusFilter.value);
    }

    // Rating filter
    if (ratingFilter.value) {
        filtered = filtered.filter(dealership => dealership.rating === ratingFilter.value);
    }

    // Type filter
    if (typeFilter.value) {
        filtered = filtered.filter(dealership => dealership.type === typeFilter.value);
    }

    return filtered;
});

const paginatedDealerships = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredDealerships.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredDealerships.value.length / itemsPerPage.value);
});

const totalDealerships = computed(() => filteredDealerships.value.length);

// Methods
const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = null;
    ratingFilter.value = null;
    typeFilter.value = null;
    currentPage.value = 1;
};

const getRatingColor = (rating: string) => {
    switch (rating) {
        case 'hot': return 'text-red-600 bg-red-50';
        case 'warm': return 'text-yellow-600 bg-yellow-50';
        case 'cold': return 'text-blue-600 bg-blue-50';
        default: return 'text-gray-600 bg-gray-50';
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'text-green-600 bg-green-50';
        case 'inactive': return 'text-gray-600 bg-gray-50';
        case 'imported': return 'text-blue-600 bg-blue-50';
        default: return 'text-gray-600 bg-gray-50';
    }
};

const getDevStatusColor = (devStatus: string) => {
    switch (devStatus) {
        case 'completed': return 'text-green-600 bg-green-50';
        case 'in_progress': return 'text-blue-600 bg-blue-50';
        case 'on_hold': return 'text-yellow-600 bg-yellow-50';
        case 'not_started': return 'text-gray-600 bg-gray-50';
        default: return 'text-gray-600 bg-gray-50';
    }
};

const formatAddress = (dealership: Dealership) => {
    const parts = [dealership.address, dealership.city, dealership.state, dealership.zip_code].filter(Boolean);
    return parts.join(', ') || 'No address';
};

// Get unique values for filters
const uniqueStatuses = computed(() => [...new Set(props.dealerships.map(d => d.status))]);
const uniqueRatings = computed(() => [...new Set(props.dealerships.map(d => d.rating))]);
const uniqueTypes = computed(() => [...new Set(props.dealerships.map(d => d.type))]);
</script>

<template>
    <Head title="Dealerships Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dealerships</h1>
                    <p class="text-muted-foreground">
                        Manage and track your dealership relationships
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="clearFilters" v-if="searchQuery || statusFilter || ratingFilter || typeFilter">
                        Clear Filters
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Dealerships</CardTitle>
                        <Building2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalDealerships.toLocaleString() }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.dealerships.filter(d => d.status === 'active').length.toLocaleString() }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Hot Leads</CardTitle>
                        <Star class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.dealerships.filter(d => d.rating === 'hot').length.toLocaleString() }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">In Development</CardTitle>
                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.dealerships.filter(d => d.in_development).length.toLocaleString() }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input 
                        placeholder="Search dealerships..." 
                        v-model="searchQuery"
                        class="pl-8"
                    />
                </div>
                <div class="flex gap-2">
                    <!-- Status Filter -->
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" class="gap-2">
                                <Filter class="h-4 w-4" />
                                Status
                                <span v-if="statusFilter" class="ml-1 rounded bg-muted px-1 py-0.5 text-xs">
                                    {{ statusFilter }}
                                </span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent>
                            <DropdownMenuLabel>Filter by Status</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem @click="statusFilter = null">All Statuses</DropdownMenuItem>
                            <DropdownMenuItem 
                                v-for="status in uniqueStatuses" 
                                :key="status"
                                @click="statusFilter = status"
                            >
                                <span class="capitalize">{{ status }}</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Rating Filter -->
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" class="gap-2">
                                <Star class="h-4 w-4" />
                                Rating
                                <span v-if="ratingFilter" class="ml-1 rounded bg-muted px-1 py-0.5 text-xs">
                                    {{ ratingFilter }}
                                </span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent>
                            <DropdownMenuLabel>Filter by Rating</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem @click="ratingFilter = null">All Ratings</DropdownMenuItem>
                            <DropdownMenuItem 
                                v-for="rating in uniqueRatings" 
                                :key="rating"
                                @click="ratingFilter = rating"
                            >
                                <span class="capitalize">{{ rating }}</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Type Filter -->
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" class="gap-2">
                                <Building2 class="h-4 w-4" />
                                Type
                                <span v-if="typeFilter" class="ml-1 rounded bg-muted px-1 py-0.5 text-xs">
                                    {{ typeFilter }}
                                </span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent>
                            <DropdownMenuLabel>Filter by Type</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem @click="typeFilter = null">All Types</DropdownMenuItem>
                            <DropdownMenuItem 
                                v-for="type in uniqueTypes" 
                                :key="type"
                                @click="typeFilter = type"
                            >
                                <span class="capitalize">{{ type }}</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Table -->
            <Card class="flex-1">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Dealerships</CardTitle>
                            <CardDescription>
                                Showing {{ paginatedDealerships.length }} of {{ totalDealerships.toLocaleString() }} dealerships
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4 font-medium">Name</th>
                                    <th class="text-left p-4 font-medium">Location</th>
                                    <th class="text-left p-4 font-medium">Contact</th>
                                    <th class="text-left p-4 font-medium">Status</th>
                                    <th class="text-left p-4 font-medium">Rating</th>
                                    <th class="text-left p-4 font-medium">Development</th>
                                    <th class="text-left p-4 font-medium">Solution</th>
                                    <th class="text-right p-4 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="dealership in paginatedDealerships" 
                                    :key="dealership.id" 
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ dealership.name || 'Unnamed Dealership' }}</span>
                                            <span class="text-sm text-muted-foreground">{{ dealership.type }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1 text-sm">
                                            <MapPin class="h-3 w-3 text-muted-foreground" />
                                            <span>{{ formatAddress(dealership) }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1 text-sm" v-if="dealership.phone">
                                            <Phone class="h-3 w-3 text-muted-foreground" />
                                            <span>{{ dealership.phone }}</span>
                                        </div>
                                        <span v-else class="text-sm text-muted-foreground">No phone</span>
                                    </td>
                                    <td class="p-4">
                                        <span 
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :class="getStatusColor(dealership.status)"
                                        >
                                            {{ dealership.status }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span 
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :class="getRatingColor(dealership.rating)"
                                        >
                                            {{ dealership.rating }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-col gap-1">
                                            <span 
                                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium w-fit"
                                                :class="getDevStatusColor(dealership.dev_status)"
                                            >
                                                {{ dealership.dev_status?.replace('_', ' ') }}
                                            </span>
                                            <span v-if="dealership.in_development" class="text-xs text-blue-600">
                                                In Development
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm">
                                            <div v-if="dealership.current_solution_name">
                                                {{ dealership.current_solution_name }}
                                            </div>
                                            <div v-else class="text-muted-foreground">No solution</div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="ghost" size="icon">
                                                    <MoreHorizontal class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem>View Details</DropdownMenuItem>
                                                <DropdownMenuItem>Edit</DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem class="text-destructive">Delete</DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between px-4 py-4 border-t" v-if="totalPages > 1">
                        <div class="text-sm text-muted-foreground">
                            Page {{ currentPage }} of {{ totalPages }}
                        </div>
                        <div class="flex items-center gap-2">
                            <Button 
                                variant="outline" 
                                size="sm" 
                                :disabled="currentPage === 1"
                                @click="currentPage--"
                            >
                                Previous
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm" 
                                :disabled="currentPage === totalPages"
                                @click="currentPage++"
                            >
                                Next
                            </Button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="paginatedDealerships.length === 0" class="text-center py-12">
                        <Building2 class="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-semibold">No dealerships found</h3>
                        <p class="mt-2 text-muted-foreground">
                            {{ searchQuery || statusFilter || ratingFilter || typeFilter 
                                ? 'Try adjusting your search or filters.' 
                                : 'No dealerships have been added yet.' 
                            }}
                        </p>
                        <Button v-if="searchQuery || statusFilter || ratingFilter || typeFilter" @click="clearFilters" class="mt-4">
                            Clear Filters
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>