import Button from '@proconvey/ui/src/components/Button'
import { H4 } from '@proconvey/ui/src/components/Headers'
import { TickIcon } from '@proconvey/ui/src/icons'
import Link from 'next/link'

type PropTypes = {
  title: string
  icon: JSX.Element,
  formId?: string
  propertyId?: string
  description: string
  image?: string
  totalQuestions: number
  completed?: boolean
  completedQuestions: number
  url: string

}

const PropertyFormCard = ({
  title,
  icon,
  description,
  totalQuestions,
  completedQuestions,
  formId,
  propertyId,
  completed,
}: PropTypes) => {
  const percentage = (completedQuestions / totalQuestions) * 100

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 py-[1.25rem] gap-3">
      <div className="flex items-center justify-start flex-grow">
        {icon}

        <div className="flex flex-col flex-grow gap-1 max-w-[600px] w-full mr-3">
          <H4 className="leading-[1.4375rem]">{title}</H4>
          <p className="leading-4 text-body text-opacity-60">{description}</p>
        </div>
      </div>

      <div className="flex items-center flex-grow md:justify-end">
        <div className="w-[250px] mr-12">
          <p className="mb-2 text-base font-bold text-body">
            {completedQuestions} of {totalQuestions} question{totalQuestions > 1 && 's'} completed
          </p>
          <div className="w-full bg-mull bg-opacity-10 h-[10px] rounded-[33px]">
            <div className={'bg-mull h-[10px] rounded-[33px] text-transparent'} style={{ width: `${Math.min(percentage, 100)}%` }}></div>
          </div>
        </div>

        <div>
          {!completed ? (
            <Button variant="secondary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]" disabled>Continue</Button>
          ) : percentage < 100 ? (
            <Link href={`/properties/${propertyId}/forms/${formId}`}>
              <Button variant="secondary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]">Continue</Button>
            </Link>
          ) : (
            <div className="flex items-center justify-center gap-2 py-[0.625rem] rounded-lg text-mint bg-mint bg-opacity-10  !px-[25px] w-[165px]">
              <TickIcon className="flex-shrink-0 w-4 h-3 text-mint" />
              <p className="text-base font-normal leading-4 rounded-lg">Completed</p>
            </div>
          )}
        </div>
      </div>

    </div>
  )
}

export default PropertyFormCard
