import Logo from '@proconvey/ui/src/svgs/logo'
import IconButton from '@proconvey/ui/src/components/IconButton'
import { BellIcon } from '@proconvey/ui/src/icons'
import Profile from '@proconvey/ui/src/components/Profile'
import { useRouter } from 'next/router'
import ProgressSidebar from '@proconvey/ui/src/components/ProgressSidebar'
import calculatePercentage from 'hooks/helpers/calculatePercentage'
import useLogout from 'hooks/useLogout'
import Skeleton from 'react-loading-skeleton'
import sectionSteps, { checkConditionsMet }  from 'helpers/steps'
import { Property } from 'gql/graphql'
import Link from 'next/link'

const MainContent = ({ children }: { children: React.ReactNode }) => (
  <div className="w-full min-h-screen bg-outlined">
    {children}
  </div>
)

type FormLayoutPropTypes = {
  children: React.ReactNode
  property?: Property,
  isLoading: boolean
}

const FormLayout = ({
  children,
  property,
  isLoading,
}: FormLayoutPropTypes) => {
  const router = useRouter()

  const { user, handleLogout } = useLogout()

  const formId = router.query.formId as string
  const myProgressAnswers = property?.my_progress?.provided_answers.filter((item) => item.active_form_id === formId)

  const sections = property?.active_forms.find(form => form.pivot?.id === formId)?.sections?.filter(section => checkConditionsMet(
    section.conditions,
    [],
    myProgressAnswers ?? [],
  ))

  return (
    <>
      <div className="flex justify-between items-center w-full px-5 sm:px-[3.125rem] pt-[1.375rem] pb-[1.125rem] border-b border-primary/15">
        <div className="flex">
          <Link href="/properties">
            <Logo className="w-[135px]" />
          </Link>
        </div>

        <div className="flex justify-end gap-16">
          <div className="flex gap-7">
            <IconButton icon={<BellIcon />} />
            <Profile user={{ first_name: user.first_name ?? '', last_name: user.last_name ?? '', profile_image: user.profile_image }}>
              <Profile.Item onClick={handleLogout}>Logout</Profile.Item>
            </Profile>
          </div>
        </div>
      </div>

      <div className="flex w-full h-full min-h-screen">
        <div className="flex-col hidden min-h-screen border-r border-primary/15 md:flex">

          <ProgressSidebar>
            {
              isLoading
                ? <div className="flex flex-col w-full gap-4">
                  <Skeleton width={231} height={55} />
                  <Skeleton width={231} height={55} />
                  <Skeleton width={231} height={55} />
                  <Skeleton width={231} height={55} />
                  <Skeleton width={231} height={55} />
                  <Skeleton width={231} height={55} />
                </div>
                : (
                  sections?.map((section) => {
                    let number = 0
                    const sectionAnswers = myProgressAnswers?.filter(a => a.answer.step.section.id === section.id)
                    const isComplete = sectionSteps(section.steps, property)?.filter((step) => sectionAnswers?.filter(fa => step!.answers.map((a: any) => a.id).includes(fa?.answer.id)).length).length > 0

                    sectionSteps(section.steps, property).forEach((step) => {
                      const stepAnswers = myProgressAnswers?.filter(answer => answer.answer.step.id === step!.id)
                      const isStepComplete = sectionSteps(section.steps, property).every(step => step!.answers?.filter((answer: any) => stepAnswers?.filter(filteredAnswer => step!.answers.map((answer: any) => answer.id).includes(answer.id) && filteredAnswer?.value !== null).length).length > 0)

                      if (isStepComplete) {
                        number += 1
                      }
                    })

                    const progress = calculatePercentage(number, sectionSteps(section.steps, property).length)

                    return (
                      <ProgressSidebar.Item collapsed={false} active={section.id === router.query.sectionId} completed={isComplete} progress={progress} text={section.name} key={section.id}>
                        {
                          sectionSteps(section.steps, property).map((step, number = 0) => {
                            const stepAnswers = myProgressAnswers?.filter(a => a.answer.step.id === step!.id)
                            const isComplete = step!.answers?.filter((answer: any) => stepAnswers?.filter(fa => step!.answers.map((a: any) => a.id).includes(answer.id) && fa?.value !== null).length).length > 0

                            return (<ProgressSidebar.SubItem index={1} lastChild={false} collapsed={false} active={true} completed={isComplete} key={step!.id}>Question {number + 1}</ProgressSidebar.SubItem>)
                          })
                        }
                      </ProgressSidebar.Item>
                    )
                  })
                )
            }
          </ProgressSidebar>
        </div>
        {children}
      </div>
    </>
  )
}

FormLayout.MainContent = MainContent

export default FormLayout
