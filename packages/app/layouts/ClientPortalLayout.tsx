import { BellIcon, DocumentIcon, HamburgerIcon, OverviewIcon, PackIcon, UserIcon } from '@proconvey/ui/src/icons'
import Logo from '@proconvey/ui/src/svgs/logo'
import Menu from '@proconvey/ui/src/components/Menu'
import Profile from '@proconvey/ui/src/components/Profile'
import { useRouter } from 'next/router'
import NotificationsDropdown from '@proconvey/ui/src/components/NotificationsDropdown'
import Link from 'next/link'
import useNotifications from 'hooks/useNotifications'
import useLogout from 'hooks/useLogout'
import IconButton from '@proconvey/ui/src/components/IconButton'
import useSidebar from 'hooks/useSidebar'
import ModalDrawer from '@proconvey/ui/src/components/ModalDrawer'

const MainContent = ({ children }: { children: React.ReactNode }) => (
  <div className="flex w-full overflow-y-auto bg-outlined">
    <div className="max-w-[1450px] mx-auto flex flex-col flex-grow">
      {children}
    </div>
  </div>
)

const ClientPortalLayout = ({ children }: { children: React.ReactNode }) => {
  const router = useRouter()

  const { user, handleLogout } = useLogout()

  const { markAllNotificationsRead, fetching: isFetchingNotifications } = useNotifications()

  const notifications = user.unread_notifications

  const { isSidebarOpen, setIsSidebarOpen } = useSidebar()

  const getMenuContent = (isMobile: boolean = false) => {
    return <>
      {
        (router.pathname === '/properties' || router.pathname === '/client-profile' || router.pathname === '/notifications') &&
          <Link href="/properties" className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
            <Menu.Item icon={<UserIcon />} active={router.pathname.startsWith('/properties')}>
              Properties
            </Menu.Item>
          </Link>
      }

      {
        router.pathname.startsWith('/properties/[id]') ?
          <>
            <Link href={`/properties/${router.query.id}/`} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.Item icon={<OverviewIcon />} active={router.pathname === '/properties/[id]'}>
                Overview
              </Menu.Item>
            </Link>

            <Link href={`/properties/${router.query.id}/documents`} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.Item icon={<DocumentIcon />} active={router.pathname === '/properties/[id]/documents'}>
                Documents
              </Menu.Item>
            </Link>

            <Link href={`/properties/${router.query.id}/pack`} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
              <Menu.Item icon={<PackIcon />} active={router.pathname === '/properties/[id]/pack'}>
                My Pack
              </Menu.Item>
            </Link>
          </>
          : null
      }

      <Link href={'/notifications'} className="flex" onClick={() => isMobile && setIsSidebarOpen(false)}>
        <Menu.Item icon={<BellIcon />} active={router.pathname === '/notifications'}>
          Notifications
        </Menu.Item>
      </Link>
    </>
  }

  return (
    <div className="flex flex-col h-full">
      <div className="flex gap-5 items-center w-full px-5 lg:px-[3.125rem] pt-[1.375rem] pb-[1.125rem] border-b border-primary/15">
        <div className="lg:hidden">
          <IconButton icon={<HamburgerIcon />} onClick={() => setIsSidebarOpen(true)} />
        </div>

        <div className="max-w-[275px] w-full flex-shrink">
          <Link href="/properties">
            <Logo className="w-full sm:w-[135px] max-w-[135px]" />
          </Link>
        </div>

        <div className="flex justify-end gap-16 ml-auto">

          <div className="z-10 flex gap-2 lg:gap-7">
            <NotificationsDropdown length={notifications?.length ?? 0} markAllNotificationsRead={markAllNotificationsRead} isLoading={isFetchingNotifications}>
              {
                notifications && notifications.map((notification) => (
                  <NotificationsDropdown.Item key={`notification-${notification?.id}`} notificationData={notification?.data ?? undefined} timestamp={notification?.created_at}>
                    {notification?.data?.message}
                  </NotificationsDropdown.Item>
                ))
              }
            </NotificationsDropdown>
            <Profile user={{ first_name: user.first_name ?? '', last_name: user.last_name ?? '', profile_image: user.profile_image }}>

              <Profile.Item>
                <Link href="/client-profile" className="flex whitespace-nowrap">
                  My Profile
                </Link>
              </Profile.Item>
              <Profile.Item onClick={handleLogout}>Logout</Profile.Item>
            </Profile>
          </div>
        </div>

      </div>

      <div className="flex flex-grow w-full min-h-0">
        {/* Desktop Menu */}
        <div className="flex-col max-w-[294px] w-full border-r border-primary/15 bg-white hidden lg:flex">
          <Menu>
            {getMenuContent()}
          </Menu>
        </div>

        {/* Mobile Menu */}
        <ModalDrawer isOpen={isSidebarOpen} setIsOpen={(isOpen: boolean) => setIsSidebarOpen(isOpen)}>
          {getMenuContent(true)}
        </ModalDrawer>

        {children}
      </div>
    </div>
  )
}

ClientPortalLayout.MainContent = MainContent

export default ClientPortalLayout
