import Profile from '@proconvey/ui/src/components/Profile'
import StepSidebar from '@proconvey/ui/src/components/StepSidebar'
import Logo from '@proconvey/ui/src/svgs/logo'
import useLogout from 'hooks/useLogout'

const MainContent = ({ children }: { children: React.ReactNode }) => (
  <div className="w-full min-h-screen bg-outlined py-[3.125rem] px-[1.875rem]">
    <div className="w-full max-w-[1450px] mx-auto">
      {children}
    </div>
  </div>
)

const SetupLayout = ({ children, currentStep }: { children: React.ReactNode, currentStep: number }) => {

  const { user, handleLogout } = useLogout()

  return (
    <div>
      <div className="flex justify-between gap-5 w-full px-5 lg:px-[3.125rem] pt-[1.375rem] pb-[1.125rem]">
        <Logo className="w-[8.4375rem] h-[3.125rem]" />
        <Profile user={{ first_name: user?.first_name ?? '', last_name: user?.last_name ?? '', profile_image: user?.profile_image }}>
          <Profile.Item onClick={handleLogout}>Logout</Profile.Item>
        </Profile>
      </div>

      <hr className="text-purple" />

      <div className="flex w-full h-full min-h-screen">
        <div className="flex-col max-w-[294px] w-full min-h-screen border-r border-r-primary border-opacity-10 bg-white hidden md:flex">
          <div className="ml-[3.125rem]">
            <StepSidebar currentStep={currentStep}>
              <StepSidebar.Item>Profile</StepSidebar.Item>
              <StepSidebar.Item>Business</StepSidebar.Item>
              <StepSidebar.Item>Onboarding letters</StepSidebar.Item>
              <StepSidebar.Item>Payment on account setup</StepSidebar.Item>
              <StepSidebar.Item>ID checks setup</StepSidebar.Item>
              <StepSidebar.Item>Invite team members</StepSidebar.Item>
            </StepSidebar>
          </div>
        </div>
        {children}
      </div>
    </div>
  )
}

SetupLayout.MainContent = MainContent

export default SetupLayout
