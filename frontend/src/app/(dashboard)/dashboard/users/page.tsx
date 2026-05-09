import UsersView from '../../../../views/users/index';

export default function Page() {
    // Server component entry for Admin Users page — delegates to client UsersView
    return (
        <div>
            <UsersView />
        </div>
    );
}
