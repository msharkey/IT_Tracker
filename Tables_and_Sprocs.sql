USE [ITChangeTracker]
GO

/****** Object:  StoredProcedure [dbo].[usp_getAccountManagers]    Script Date: 5/4/2016 11:03:47 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO
Create PROCEDURE [dbo].[usp_getAccountManagers]
 AS
Select '' as sub_name
UNION ALL

SELECT Employee_name
FROM [ITChangeTracker].[dbo].[employee] e
JOIN [ITChangeTracker].[dbo].[Department] d
ON e.department_ID = d.department_id
Where d.Department_name = 'Account Management'
Order By sub_name
GO


USE [ITChangeTracker]
GO

/****** Object:  StoredProcedure [dbo].[usp_Ext_RequestType]    Script Date: 5/4/2016 11:09:08 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

Create procedure [dbo].[usp_Ext_RequestType]

AS

Select '' as [request_type]
UNION ALL

SELECT  [request_type]
FROM [ITChangeTracker].[dbo].[request_type]
order by request_type
GO



SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO
Create PROCEDURE [dbo].[usp_Ext_Analyst]
 AS
Select '' as Analyst_Name
UNION ALL

SELECT Employee_name
FROM [ITChangeTracker].[dbo].[employee] e
JOIN [ITChangeTracker].[dbo].[Department] d
ON e.department_ID = d.department_id
Where d.Department_name = 'Data Team'
Order By Analyst_Name
GO


USE [ITChangeTracker]
GO

/****** Object:  StoredProcedure [dbo].[usp_Ext_DateRange]    Script Date: 5/4/2016 11:15:28 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


Create procedure [dbo].[usp_Ext_DateRange]

AS

Select Convert(Varchar(20),daterange,101) as daterange
from [ITChangeTracker].[dbo].[PopulateDates](Getdate())
Where daterange Between DATEADD(dd,-1,Getdate()) and DATEADD(dd,30,Getdate())
							
GO



Alter Procedure dbo.usp_Ext_ticketDetails
@username Varchar(20),
@ticket_status Varchar(20)
AS
 Select request_id , CONVERT(Varchar (20),date_submitted,101) as date_submitted , s.Employee_name,request_desc,request_importance,ISNULL(resolution,'') as Resolution,ISNULL(Convert(Varchar(20),Request_complete_date,101),'') as Completed_Date,Request_type
						   ,Convert(Varchar(20),requiredDue_Date,101) as requiredDue_Date
						   ,e.employee_name as AnalystAssigned
						from dbo.request r
						JOIN dbo.employee e
							ON r.assigned_to = e.employee_id
							JOIN dbo.employee s 
								ON s.employee_id = request_by
								JOIN dbo.request_type rt
								ON rt.request_type_id = r.request_type_id
								Where e.userName = @username
								 and (
										(@ticket_status = '' and request_complete_date IS NULL)
									 OR (@ticket_status = 'Open' and request_complete_date IS NULL)
									 OR (@ticket_status = 'Closed' and request_complete_date IS NOT NULL)	
									)
								Order by date_submitted


Create Procedure dbo.usp_Ext_ticketIDs
@username  Varchar(50)
AS
Select ' ' as request_id
UNION ALL
Select Cast(request_id as Varchar(20))
from dbo.request r
						JOIN dbo.employee e
							ON r.assigned_to = e.employee_id
							JOIN dbo.employee s 
								ON s.employee_id = request_by
								JOIN dbo.request_type rt
								ON rt.request_type_id = r.request_type_id
								Where e.userName = @username
								and resolution is NULL

USE [ITChangeTracker]
GO

/****** Object:  StoredProcedure [dbo].[usp_ClsTicket]    Script Date: 5/4/2016 11:29:45 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


Alter Procedure [dbo].[usp_ClsTicket]
@resolution Varchar (4000),
@request_id INT
AS

/******************************    
** Name: usp_ClsTicket
** Desc: Updates ticket details
** Auth: Group 4
** Date: 04/27/2016
**************************
** Change History
**************************
** PR   Date	    Author  Description	
** --   --------   -------   ------------------------------------

*******************************/


Update rq
SET rq.request_complete_date = Getdate(), rq.Resolution = @resolution
from dbo.request rq
Where request_id =@request_id


Declare @analyst_email Varchar(50),
		@sub_name Varchar(50),
		@requestdesc Varchar(50),
		@sub_email Varchar(50)


Select @sub_email= sub.Employee_email, @analyst_email= ana.employee_email,@requestdesc = request_desc, @sub_name = sub.employee_name
from dbo.request r
JOIN dbo.employee sub
ON sub.employee_id = r.request_by
JOIN dbo.employee ana
ON ana.employee_id = r.assigned_to
Where r.request_id = @request_id



Declare @emailSubject Varchar(100)
SET @emailSubject = 'Ticket number '+Cast(@request_id as Varchar(20))+' Closed'

Declare @emailbody NVarchar(MAX)
SET @emailbody = N'Hi '+@sub_name+N','+
		 
		 
		 N'<br /><br />Your ticket has been closed.  Please review the resolution below.
		 
		  <br /><br />Thank you for using IT Tracker.  Please help improve our service by taking our survey here: <p><a href= "http://submititticket.com/It_tracker/Survey.php">Survey</a></p>
		 
		 <br /><br />Thank you,
		 
		 <br /><br />Data Team

		 <br /><br />Ticket ID: ' +Cast(@request_id as Varchar(20))+

		 N' <br /><br />Request:'+@requestdesc+

		 N'<br /><br />Resolution:'+@resolution



Exec msdb.dbo.Sp_send_dbmail @profile_name = 'Local Server', @from_address = 'ITtaskTracker@noreply.com', @subject = @emailSubject ,@recipients =@sub_email,@copy_recipients = @analyst_email,@body = @emailbody, @body_format = 'HTML'
		 

GO


USE [ITChangeTracker]
GO

/****** Object:  StoredProcedure [dbo].[usp_insrtNewticket]    Script Date: 5/4/2016 11:31:55 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

 CREATE Procedure [dbo].[usp_insrtNewticket]
	@sub_name Varchar(50),
	@request_type Varchar(50),
	@analyst_name varchar(50),
	@priority_level varchar(20),
	@daterange varchar(20),
	@request_desc varchar(4000)
 AS



/******************************    
** Name: usp_insrtNewticket
** Desc: Inserts request and sends email to Account Manager and Analyst
** Auth: Group 4
** Date: 04/26/2016
**************************
** Change History
**************************
** PR   Date	    Author  Description	
** --   --------   -------   ------------------------------------
** 1    04/27/2016  Matt	 Added request desc and request type Input and details to email
** 1    04/27/2016  Matt	 Added analyst to recipiants and removed from CC on send mail proc
*******************************/


 Declare @daterange_toDate Datetime,
		 @request_by INT,
		 @assigned_to INT,
		 @request_typeID INT,
		 @sub_email Varchar(50),
		 @analyst_email varchar(50),
		 @request_type_id INT,
		 @ticketID INT

  SET @daterange_toDate = Cast(@daterange as datetime)

  SET @request_by = (
						  Select employee_id
						  from [ITChangeTracker].[dbo].[employee]
						  Where employee_name = @sub_name
					)
   SET @assigned_to = (
						  Select employee_id
						  from [ITChangeTracker].[dbo].[employee]
						  Where employee_name = @analyst_name
							
					  )
	SET @request_typeID = (
							Select request_type_id
							From [ITChangeTracker].[dbo].[request_type]
							Where request_type = @request_type
						  )

	 SET @sub_email = (
						Select employee_email
						FROM [ITChangeTracker].[dbo].[employee]
						Where employee_name = @sub_name
					  )


 SET @analyst_email = (
						Select employee_email
						FROM [ITChangeTracker].[dbo].[employee]
						Where employee_name = @analyst_name
					  )

 SET @request_type_ID = (
						 Select request_type_id 
						 FROM request_type
						 Where request_type = @request_type
						)


 Insert Into [ITChangeTracker].[dbo].[request] ([date_submitted],[request_by],[assigned_to],[request_importance],[RequiredDue_date],[request_desc],[request_type_id])
Values (Getdate(),@request_by,@assigned_to,@priority_level,@daterange_toDate,@request_desc,@request_type_id)
 
 SET @ticketID = SCOPE_IDENTITY()

Declare @emailSubject Varchar(100)
SET @emailSubject = 'Ticket number '+Cast(@ticketID as Varchar(20))+' submitted'

Declare @emailbody NVarchar(MAX)
SET @emailbody = N'Hi '+@sub_name+N','+
		 
		 
		 N'<br /><br />Your ticket has been sucessfully assigned to '+@analyst_name+'.  The ticket has been marked as '+@priority_level+' priority and a requested completion of '+@daterange+'.'+
		 
		 N'<br /><br />Please contact this person directly for any questions.
		 
		 <br /><br />Thank you,
		 
		 <br /><br />Data Team

		 <br /><br />Ticket ID: ' +Cast(@ticketID as Varchar(20))+

		 N'<br /><br />Details:'+@request_desc



Exec msdb.dbo.Sp_send_dbmail @profile_name = 'Local Server', @from_address = 'ITtaskTracker@noreply.com', @subject = @emailSubject ,@recipients =@sub_email,@copy_recipients = @analyst_email,@body = @emailbody, @body_format = 'HTML'
		 
		 
GO




--Insert  Survey results

Alter Procedure dbo.usp_inst_SurveyRes
@q1 INT,
@q2 INT,
@q3 INT,
@content Varchar(8000)

as

INSERT INTO dbo.survey(TicketService,Speed,UserExper,content) VALUES (@q1,@q2,@q3,@content)



--- User Tables

USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[Department]    Script Date: 5/4/2016 1:42:47 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[Department](
	[Department_id] [int] IDENTITY(1,1) NOT NULL,
	[Department_name] [varchar](50) NOT NULL,
 CONSTRAINT [Department_pk] PRIMARY KEY CLUSTERED 
(
	[Department_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[employee]    Script Date: 5/4/2016 1:43:09 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[employee](
	[employee_id] [int] IDENTITY(1,1) NOT NULL,
	[employee_name] [varchar](75) NULL,
	[employee_email] [varchar](75) NULL,
	[department_ID] [int] NULL,
	[userName] [varchar](50) NULL,
 CONSTRAINT [employee_pk] PRIMARY KEY CLUSTERED 
(
	[employee_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[employee]  WITH CHECK ADD  CONSTRAINT [employee_fk1] FOREIGN KEY([department_ID])
REFERENCES [dbo].[Department] ([Department_id])
GO

ALTER TABLE [dbo].[employee] CHECK CONSTRAINT [employee_fk1]
GO

USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[equipment]    Script Date: 5/4/2016 1:43:16 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[equipment](
	[equipment_id] [int] IDENTITY(1,1) NOT NULL,
	[equipment_name] [varchar](50) NULL,
	[model_number] [varchar](50) NULL,
	[brand] [varchar](50) NULL,
 CONSTRAINT [equitment_pk] PRIMARY KEY CLUSTERED 
(
	[equipment_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[harddates]    Script Date: 5/4/2016 1:43:24 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[harddates](
	[daterange] [date] NULL
) ON [PRIMARY]

GO


USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[harddates2]    Script Date: 5/4/2016 1:43:34 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[harddates2](
	[daterange] [varchar](20) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[request]    Script Date: 5/4/2016 1:43:46 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[request](
	[request_id] [int] IDENTITY(1,1) NOT NULL,
	[date_submitted] [datetime] NULL,
	[date_assigned] [datetime] NULL,
	[request_complete_date] [datetime] NULL,
	[request_by] [int] NULL,
	[assigned_to] [int] NULL,
	[request_importance] [varchar](20) NULL,
	[equipment_id] [int] NULL,
	[request_type_id] [int] NULL,
	[Resolution] [varchar](max) NULL,
	[request_desc] [varchar](4000) NULL,
	[RequiredDue_date] [date] NULL,
 CONSTRAINT [request_pk] PRIMARY KEY CLUSTERED 
(
	[request_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[request]  WITH CHECK ADD  CONSTRAINT [request_Assigned] FOREIGN KEY([request_by])
REFERENCES [dbo].[employee] ([employee_id])
GO

ALTER TABLE [dbo].[request] CHECK CONSTRAINT [request_Assigned]
GO

ALTER TABLE [dbo].[request]  WITH CHECK ADD  CONSTRAINT [request_fk1] FOREIGN KEY([request_by])
REFERENCES [dbo].[employee] ([employee_id])
GO

ALTER TABLE [dbo].[request] CHECK CONSTRAINT [request_fk1]
GO

ALTER TABLE [dbo].[request]  WITH CHECK ADD  CONSTRAINT [request_type_fk1] FOREIGN KEY([equipment_id])
REFERENCES [dbo].[equipment] ([equipment_id])
GO

ALTER TABLE [dbo].[request] CHECK CONSTRAINT [request_type_fk1]
GO

ALTER TABLE [dbo].[request]  WITH CHECK ADD  CONSTRAINT [request_type_fk2] FOREIGN KEY([request_type_id])
REFERENCES [dbo].[request_type] ([request_type_id])
GO

ALTER TABLE [dbo].[request] CHECK CONSTRAINT [request_type_fk2]
GO

USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[request_type]    Script Date: 5/4/2016 1:43:56 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[request_type](
	[request_type_id] [int] IDENTITY(1,1) NOT NULL,
	[request_type] [varchar](50) NULL,
 CONSTRAINT [request_type_pk] PRIMARY KEY CLUSTERED 
(
	[request_type_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

USE [ITChangeTracker]
GO

/****** Object:  Table [dbo].[survey]    Script Date: 5/4/2016 1:44:05 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[survey](
	[TicketService] [tinyint] NULL,
	[Speed] [tinyint] NULL,
	[UserExper] [tinyint] NULL,
	[content] [varchar](8000) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


USE [ITChangeTracker]
GO

/****** Object:  View [dbo].[IttrackerRange]    Script Date: 5/4/2016 1:44:19 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

Create View [dbo].[IttrackerRange] 
AS

Select daterange
from [ITChangeTracker].[dbo].[PopulateDates](Getdate())
Where daterange Between DATEADD(dd,-1,Getdate()) and DATEADD(dd,30,Getdate())

GO



USE [ITChangeTracker]
GO

/****** Object:  UserDefinedFunction [dbo].[PopulateDates]    Script Date: 5/4/2016 1:44:41 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


Create FUNCTION [dbo].[PopulateDates](@currentDate Date)
RETURNS @dateRange TABLE 
(
 daterange Date  
)
AS 
BEGIN
	Declare @startpoint Date = dateadd(yy,-1,Getdate())
	

	WHILE @startpoint < DateAdd(yy,1,getdate())
		Begin

			Insert INTO @dateRange
			Select @startpoint

			SET @startpoint = DATEADD(dd,1,@startpoint)

		END;
		Return;
END

GO














