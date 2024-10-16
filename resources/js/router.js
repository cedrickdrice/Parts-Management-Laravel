// router.js
import { createRouter, createWebHistory } from 'vue-router';
import AdminParts from './components/Parts/Parts.vue';
import TeamParts from './components/Parts/Team/TeamParts.vue';
import { useUserStore } from './stores/user.js';

const routes = [
    {
        path: '/team/parts',
        name: 'TeamParts',
        component: TeamParts,
        meta: { requiresAuth: true, role: 'team_admin' },
    },
    {
        path: '/admin/parts',
        name: 'AdminParts',
        component: AdminParts,
        meta: { requiresAuth: true, role: 'system_admin' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();
    await userStore.fetchCurrentUser();

    const user = userStore.currentUser;
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!user) {
            return next('/login');
        } else if (to.meta.role && to.meta.role !== user.role) {
            return next('/forbidden');
        }
    }

    next();
});

export default router;
